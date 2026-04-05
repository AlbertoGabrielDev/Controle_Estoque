<?php

namespace Modules\Commercial\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Commercial\Models\CommercialSalesOrder;
use Modules\Commercial\Models\CommercialSalesOrderItem;
use RuntimeException;

class SalesOrderService
{
    public function __construct(private CommercialDocumentNumberService $numberService)
    {
    }

    /**
     * Create a new sales order with its items.
     *
     * @param array $payload
     * @return CommercialSalesOrder
     * @throws \Throwable
     */
    public function createOrder(array $payload): CommercialSalesOrder
    {
        return DB::transaction(function () use ($payload): CommercialSalesOrder {
            $order = CommercialSalesOrder::query()->create([
                'numero'          => $this->numberService->generate('SO'),
                'proposal_id'     => $payload['proposal_id'] ?? null,
                'opportunity_id'  => $payload['opportunity_id'] ?? null,
                'cliente_id'      => $payload['cliente_id'],
                'status'          => 'rascunho',
                'data_pedido'     => $payload['data_pedido'] ?? Carbon::today(),
                'observacoes'     => $payload['observacoes'] ?? null,
                'subtotal'        => 0,
                'desconto_total'  => 0,
                'total_impostos'  => 0,
                'total'           => 0,
            ]);

            $this->syncItems($order, $payload['items'] ?? []);
            $this->recalcTotals($order);

            return $order->load('items');
        });
    }

    /**
     * Create a sales order from an approved proposal.
     *
     * This is a convenience wrapper; the actual conversion is driven by ProposalService::convertToSalesOrder.
     *
     * @param int   $proposalId
     * @param array $extraPayload
     * @return CommercialSalesOrder
     * @throws \Throwable
     */
    public function createFromProposal(int $proposalId, array $extraPayload = []): CommercialSalesOrder
    {
        $proposal = \Modules\Commercial\Models\CommercialProposal::query()
            ->with('items')
            ->findOrFail($proposalId);

        if ($proposal->status !== 'aprovada') {
            throw new RuntimeException('Apenas propostas aprovadas podem ser convertidas em pedido.');
        }

        $payload = array_merge([
            'proposal_id'    => $proposal->id,
            'opportunity_id' => $proposal->opportunity_id,
            'cliente_id'     => $proposal->cliente_id,
            'observacoes'    => $proposal->observacoes,
            'items'          => $proposal->items->map(fn ($i) => [
                'item_id'            => $i->item_id,
                'descricao_snapshot' => $i->descricao_snapshot,
                'unidade_medida_id'  => $i->unidade_medida_id,
                'quantidade'         => $i->quantidade,
                'preco_unit'         => $i->preco_unit,
                'desconto_percent'   => $i->desconto_percent,
                'desconto_valor'     => $i->desconto_valor,
                'imposto_id'         => $i->imposto_id,
                'aliquota_snapshot'  => $i->aliquota_snapshot,
                'total_linha'        => $i->total_linha,
            ])->toArray(),
        ], $extraPayload);

        return $this->createOrder($payload);
    }

    /**
     * Update a draft sales order and replace its items.
     *
     * @param int   $orderId
     * @param array $payload
     * @return CommercialSalesOrder
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function updateOrder(int $orderId, array $payload): CommercialSalesOrder
    {
        return DB::transaction(function () use ($orderId, $payload): CommercialSalesOrder {
            $order = CommercialSalesOrder::query()->findOrFail($orderId);

            if ($order->status !== 'rascunho') {
                throw new RuntimeException('Apenas pedidos em rascunho podem ser editados.');
            }

            $order->update([
                'cliente_id'  => $payload['cliente_id'] ?? $order->cliente_id,
                'data_pedido' => $payload['data_pedido'] ?? $order->data_pedido,
                'observacoes' => $payload['observacoes'] ?? $order->observacoes,
            ]);

            if (isset($payload['items'])) {
                $this->syncItems($order, $payload['items']);
            }

            $this->recalcTotals($order);

            return $order->load('items');
        });
    }

    /**
     * Confirm a draft sales order, locking critical fields.
     *
     * @param int $orderId
     * @return CommercialSalesOrder
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function confirmOrder(int $orderId): CommercialSalesOrder
    {
        return DB::transaction(function () use ($orderId): CommercialSalesOrder {
            $order = CommercialSalesOrder::query()->findOrFail($orderId);

            if ($order->status !== 'rascunho') {
                throw new RuntimeException('Apenas pedidos em rascunho podem ser confirmados.');
            }

            if ($order->items()->count() === 0) {
                throw new RuntimeException('O pedido deve ter ao menos um item para ser confirmado.');
            }

            $order->update(['status' => 'confirmado']);

            return $order->fresh();
        });
    }

    /**
     * Cancel a sales order.
     *
     * @param int $orderId
     * @return CommercialSalesOrder
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function cancelOrder(int $orderId): CommercialSalesOrder
    {
        return DB::transaction(function () use ($orderId): CommercialSalesOrder {
            $order = CommercialSalesOrder::query()->findOrFail($orderId);

            if (in_array($order->status, ['faturado_total', 'fechado', 'cancelado'], true)) {
                throw new RuntimeException('Este pedido nao pode ser cancelado no status atual.');
            }

            $order->update(['status' => 'cancelado']);

            return $order->fresh();
        });
    }

    /**
     * Close a fully invoiced order.
     *
     * @param int $orderId
     * @return CommercialSalesOrder
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function closeOrder(int $orderId): CommercialSalesOrder
    {
        return DB::transaction(function () use ($orderId): CommercialSalesOrder {
            $order = CommercialSalesOrder::query()->findOrFail($orderId);

            if ($order->status !== 'faturado_total') {
                throw new RuntimeException('Apenas pedidos totalmente faturados podem ser fechados.');
            }

            $order->update(['status' => 'fechado']);

            return $order->fresh();
        });
    }

    /**
     * Recalculate and persist totals for a sales order.
     *
     * Also adjusts the order status to 'faturado_parcial' or 'faturado_total'
     * based on the sum of quantidade_faturada vs quantidade.
     *
     * @param CommercialSalesOrder $order
     * @return void
     */
    public function recalcTotals(CommercialSalesOrder $order): void
    {
        $order->loadMissing('items');

        $subtotal      = 0;
        $descontoTotal = 0;
        $totalImpostos = 0;

        foreach ($order->items as $item) {
            $precoBase    = (float) $item->preco_unit * (float) $item->quantidade;
            $desconto     = (float) $item->desconto_valor;
            $aliquota     = (float) ($item->aliquota_snapshot ?? 0);
            $totalLinha   = $precoBase - $desconto;
            $impostoLinha = round($totalLinha * ($aliquota / 100), 2);

            $subtotal      += $precoBase;
            $descontoTotal += $desconto;
            $totalImpostos += $impostoLinha;
        }

        $total = round($subtotal - $descontoTotal + $totalImpostos, 2);

        $order->update([
            'subtotal'       => round($subtotal, 2),
            'desconto_total' => round($descontoTotal, 2),
            'total_impostos' => round($totalImpostos, 2),
            'total'          => $total,
        ]);
    }

    /**
     * Update the order billing status based on invoiced quantities.
     *
     * Called by InvoiceService after each invoice operation.
     *
     * @param CommercialSalesOrder $order
     * @return void
     */
    public function syncInvoiceStatus(CommercialSalesOrder $order): void
    {
        $order->loadMissing('items');

        $allInvoiced = $order->items->every(
            fn ($item) => (float) $item->quantidade_faturada >= (float) $item->quantidade
        );

        $anyInvoiced = $order->items->some(
            fn ($item) => (float) $item->quantidade_faturada > 0
        );

        if ($allInvoiced) {
            $order->update(['status' => 'faturado_total']);
        } elseif ($anyInvoiced) {
            $order->update(['status' => 'faturado_parcial']);
        } elseif (!in_array($order->status, ['cancelado', 'fechado'], true)) {
            $order->update(['status' => 'confirmado']);
        }
    }

    /**
     * Sync items for a sales order: delete existing and insert new ones.
     *
     * @param CommercialSalesOrder $order
     * @param array                $items
     * @return void
     */
    private function syncItems(CommercialSalesOrder $order, array $items): void
    {
        $order->items()->delete();

        foreach ($items as $item) {
            CommercialSalesOrderItem::query()->create([
                'order_id'           => $order->id,
                'item_id'            => $item['item_id'],
                'descricao_snapshot' => $item['descricao_snapshot'],
                'unidade_medida_id'  => $item['unidade_medida_id'] ?? null,
                'quantidade'         => $item['quantidade'],
                'quantidade_faturada'=> 0,
                'preco_unit'         => $item['preco_unit'],
                'desconto_percent'   => $item['desconto_percent'] ?? 0,
                'desconto_valor'     => $item['desconto_valor'] ?? 0,
                'imposto_id'         => $item['imposto_id'] ?? null,
                'aliquota_snapshot'  => $item['aliquota_snapshot'] ?? null,
                'total_linha'        => $item['total_linha'],
            ]);
        }
    }
}
