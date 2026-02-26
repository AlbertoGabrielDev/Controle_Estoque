<?php

namespace Modules\Purchases\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Models\PurchaseOrderItem;
use Modules\Purchases\Models\PurchaseQuotation;
use Modules\Purchases\Models\PurchaseQuotationSupplierItem;
use RuntimeException;

class PurchaseOrderService
{
    public function __construct(private DocumentNumberService $numberService)
    {
    }

    /**
     * Create purchase orders from a closed quotation.
     *
     * @param int $quotationId
     * @return \Modules\Purchases\Models\PurchaseOrder[]
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function createFromQuotation(int $quotationId): array
    {
        return DB::transaction(function () use ($quotationId): array {
            $quotation = PurchaseQuotation::query()->findOrFail($quotationId);

            if ($quotation->status !== 'encerrada') {
                throw new RuntimeException('A cotacao precisa estar encerrada para gerar pedidos.');
            }

            $selectedItems = PurchaseQuotationSupplierItem::query()
                ->with(['quotationSupplier', 'requisitionItem'])
                ->whereHas('quotationSupplier', function ($query) use ($quotationId) {
                    $query->where('quotation_id', $quotationId);
                })
                ->where('selecionado', true)
                ->get();

            if ($selectedItems->isEmpty()) {
                throw new RuntimeException('Nenhum item vencedor encontrado para gerar pedidos.');
            }

            $orders = [];
            $grouped = $selectedItems->groupBy(function (PurchaseQuotationSupplierItem $item) {
                return $item->quotationSupplier->supplier_id;
            });

            foreach ($grouped as $supplierId => $items) {
                $order = PurchaseOrder::query()->create([
                    'numero' => $this->numberService->generate('PO'),
                    'status' => 'emitido',
                    'supplier_id' => (int) $supplierId,
                    'quotation_id' => $quotationId,
                    'data_emissao' => Carbon::today(),
                    'data_prevista' => null,
                    'observacoes' => null,
                    'total' => 0,
                ]);

                $total = 0;

                foreach ($items as $item) {
                    $lineTotal = (float) $item->quantidade * (float) $item->preco_unit;

                    PurchaseOrderItem::query()->create([
                        'order_id' => $order->id,
                        'item_id' => $item->item_id,
                        'descricao_snapshot' => $item->requisitionItem->descricao_snapshot,
                        'unidade_medida_id' => $item->requisitionItem->unidade_medida_id,
                        'quantidade_pedida' => $item->quantidade,
                        'quantidade_recebida' => 0,
                        'preco_unit' => $item->preco_unit,
                        'imposto_id' => $item->imposto_id,
                        'aliquota_snapshot' => $item->aliquota_snapshot,
                        'total_linha' => $lineTotal,
                    ]);

                    $total += $lineTotal;
                }

                $order->update(['total' => $total]);
                $orders[] = $order->refresh();
            }

            return $orders;
        });
    }

    /**
     * Cancel a purchase order.
     *
     * @param int $orderId
     * @return \Modules\Purchases\Models\PurchaseOrder
     * @throws \RuntimeException
     */
    public function cancelOrder(int $orderId): PurchaseOrder
    {
        $order = PurchaseOrder::query()->findOrFail($orderId);

        if ($order->status === 'fechado') {
            throw new RuntimeException('Pedidos fechados nao podem ser cancelados.');
        }

        $order->status = 'cancelado';
        $order->save();

        return $order->refresh();
    }

    /**
     * Close a purchase order.
     *
     * @param int $orderId
     * @return \Modules\Purchases\Models\PurchaseOrder
     * @throws \RuntimeException
     */
    public function closeOrder(int $orderId): PurchaseOrder
    {
        $order = PurchaseOrder::query()->findOrFail($orderId);

        if ($order->status !== 'recebido') {
            throw new RuntimeException('Somente pedidos recebidos podem ser fechados.');
        }

        $order->status = 'fechado';
        $order->save();

        return $order->refresh();
    }

    /**
     * Recalculate totals for a purchase order.
     *
     * @param int $orderId
     * @return \Modules\Purchases\Models\PurchaseOrder
     */
    public function recalcTotals(int $orderId): PurchaseOrder
    {
        $order = PurchaseOrder::query()->with('items')->findOrFail($orderId);

        $total = $order->items->sum('total_linha');
        $order->update(['total' => $total]);

        return $order->refresh();
    }
}
