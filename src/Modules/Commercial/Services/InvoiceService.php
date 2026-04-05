<?php

namespace Modules\Commercial\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Commercial\Models\CommercialSalesInvoice;
use Modules\Commercial\Models\CommercialSalesInvoiceItem;
use Modules\Commercial\Models\CommercialSalesOrder;
use Modules\Commercial\Models\CommercialSalesOrderItem;
use RuntimeException;

class InvoiceService
{
    public function __construct(
        private CommercialDocumentNumberService $numberService,
        private SalesOrderService $orderService,
        private AccountsReceivableIntegrationService $arService,
    ) {
    }

    /**
     * Create a full invoice from a confirmed sales order, billing all remaining items.
     *
     * @param int   $orderId
     * @param array $payload  Optional overrides (data_emissao, data_vencimento, observacoes).
     * @return CommercialSalesInvoice
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function createInvoice(int $orderId, array $payload = []): CommercialSalesInvoice
    {
        $order = CommercialSalesOrder::query()
            ->with('items')
            ->findOrFail($orderId);

        $items = $order->items->map(fn ($oi) => [
            'order_item_id'      => $oi->id,
            'item_id'            => $oi->item_id,
            'descricao_snapshot' => $oi->descricao_snapshot,
            'quantidade_faturada'=> (float) $oi->quantidade - (float) $oi->quantidade_faturada,
            'preco_unit'         => $oi->preco_unit,
            'desconto_percent'   => $oi->desconto_percent,
            'desconto_valor'     => $oi->desconto_valor,
            'imposto_id'         => $oi->imposto_id,
            'aliquota_snapshot'  => $oi->aliquota_snapshot,
        ])->filter(fn ($i) => $i['quantidade_faturada'] > 0)->values()->toArray();

        return $this->createPartialInvoice($orderId, $items, $payload);
    }

    /**
     * Create a partial invoice for a subset of order items or partial quantities.
     *
     * @param int   $orderId
     * @param array $items    Each entry: order_item_id, item_id, descricao_snapshot, quantidade_faturada, preco_unit, ...
     * @param array $payload  Optional (data_emissao, data_vencimento, observacoes).
     * @return CommercialSalesInvoice
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function createPartialInvoice(int $orderId, array $items, array $payload = []): CommercialSalesInvoice
    {
        return DB::transaction(function () use ($orderId, $items, $payload): CommercialSalesInvoice {
            $order = CommercialSalesOrder::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($orderId);

            if (!in_array($order->status, ['confirmado', 'faturado_parcial'], true)) {
                throw new RuntimeException('Apenas pedidos confirmados ou parcialmente faturados podem ser faturados.');
            }

            if (empty($items)) {
                throw new RuntimeException('Ao menos um item deve ser informado para o faturamento.');
            }

            $invoice = CommercialSalesInvoice::query()->create([
                'numero'         => $this->numberService->generate('INV'),
                'order_id'       => $order->id,
                'cliente_id'     => $order->cliente_id,
                'status'         => 'emitida',
                'data_emissao'   => $payload['data_emissao'] ?? Carbon::today(),
                'data_vencimento'=> $payload['data_vencimento'] ?? null,
                'observacoes'    => $payload['observacoes'] ?? null,
                'subtotal'       => 0,
                'desconto_total' => 0,
                'total_impostos' => 0,
                'total'          => 0,
            ]);

            foreach ($items as $itemData) {
                $orderItem = CommercialSalesOrderItem::query()
                    ->where('id', $itemData['order_item_id'])
                    ->where('order_id', $orderId)
                    ->lockForUpdate()
                    ->firstOrFail();

                $remaining = (float) $orderItem->quantidade - (float) $orderItem->quantidade_faturada;
                $qty = (float) $itemData['quantidade_faturada'];

                if ($qty <= 0) {
                    continue;
                }

                if ($qty > $remaining) {
                    throw new RuntimeException(
                        sprintf('Item "%s": quantidade a faturar (%s) excede o saldo disponivel (%s).', $itemData['descricao_snapshot'], $qty, $remaining)
                    );
                }

                $aliquota     = (float) ($itemData['aliquota_snapshot'] ?? 0);
                $precoBase    = (float) $itemData['preco_unit'] * $qty;
                $desconto     = (float) ($itemData['desconto_valor'] ?? 0);
                $totalLinha   = $precoBase - $desconto;
                $totalLinha   = round($totalLinha + ($totalLinha * $aliquota / 100), 2);

                CommercialSalesInvoiceItem::query()->create([
                    'invoice_id'          => $invoice->id,
                    'order_item_id'       => $orderItem->id,
                    'item_id'             => $itemData['item_id'],
                    'descricao_snapshot'  => $itemData['descricao_snapshot'],
                    'quantidade_faturada' => $qty,
                    'preco_unit'          => $itemData['preco_unit'],
                    'desconto_percent'    => $itemData['desconto_percent'] ?? 0,
                    'desconto_valor'      => $itemData['desconto_valor'] ?? 0,
                    'imposto_id'          => $itemData['imposto_id'] ?? null,
                    'aliquota_snapshot'   => $itemData['aliquota_snapshot'] ?? null,
                    'total_linha'         => $totalLinha,
                ]);

                $orderItem->increment('quantidade_faturada', $qty);
            }

            $this->recalcTotals($invoice);

            // Sync the parent order billing status
            $order->refresh();
            $this->orderService->syncInvoiceStatus($order);

            // Generate accounts receivable
            $this->arService->createReceivableFromInvoice($invoice);

            return $invoice->load('items');
        });
    }

    /**
     * Mark an invoice as issued (transition from emitida to itself — hook for future NF-e integration).
     *
     * @param int $invoiceId
     * @return CommercialSalesInvoice
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function issueInvoice(int $invoiceId): CommercialSalesInvoice
    {
        return DB::transaction(function () use ($invoiceId): CommercialSalesInvoice {
            $invoice = CommercialSalesInvoice::query()->findOrFail($invoiceId);

            if ($invoice->status !== 'emitida') {
                throw new RuntimeException('Apenas faturas emitidas podem ser confirmadas.');
            }

            // Status stays 'emitida'; reserved for future NF-e integration.
            return $invoice->fresh();
        });
    }

    /**
     * Cancel an issued invoice.
     *
     * @param int $invoiceId
     * @return CommercialSalesInvoice
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function cancelInvoice(int $invoiceId): CommercialSalesInvoice
    {
        return DB::transaction(function () use ($invoiceId): CommercialSalesInvoice {
            $invoice = CommercialSalesInvoice::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($invoiceId);

            if (!in_array($invoice->status, ['emitida', 'parcial'], true)) {
                throw new RuntimeException('Apenas faturas emitidas ou parciais podem ser canceladas.');
            }

            // Reverse quantidade_faturada on order items
            foreach ($invoice->items as $invoiceItem) {
                CommercialSalesOrderItem::query()
                    ->where('id', $invoiceItem->order_item_id)
                    ->decrement('quantidade_faturada', (float) $invoiceItem->quantidade_faturada);
            }

            $invoice->update(['status' => 'cancelada']);

            // Cancel associated receivable
            $this->arService->reverseReceivableFromReturn(
                $invoice->receivables()->where('status', 'aberto')->get()
            );

            // Re-sync order status
            $order = $invoice->order;
            if ($order) {
                $order->refresh();
                $this->orderService->syncInvoiceStatus($order);
            }

            return $invoice->fresh();
        });
    }

    /**
     * Recalculate and persist totals for an invoice.
     *
     * @param CommercialSalesInvoice $invoice
     * @return void
     */
    public function recalcTotals(CommercialSalesInvoice $invoice): void
    {
        $invoice->loadMissing('items');

        $subtotal      = 0;
        $descontoTotal = 0;
        $totalImpostos = 0;

        foreach ($invoice->items as $item) {
            $precoBase    = (float) $item->preco_unit * (float) $item->quantidade_faturada;
            $desconto     = (float) $item->desconto_valor;
            $aliquota     = (float) ($item->aliquota_snapshot ?? 0);
            $totalLinha   = $precoBase - $desconto;
            $impostoLinha = round($totalLinha * ($aliquota / 100), 2);

            $subtotal      += $precoBase;
            $descontoTotal += $desconto;
            $totalImpostos += $impostoLinha;
        }

        $total = round($subtotal - $descontoTotal + $totalImpostos, 2);

        $invoice->update([
            'subtotal'       => round($subtotal, 2),
            'desconto_total' => round($descontoTotal, 2),
            'total_impostos' => round($totalImpostos, 2),
            'total'          => $total,
        ]);
    }
}
