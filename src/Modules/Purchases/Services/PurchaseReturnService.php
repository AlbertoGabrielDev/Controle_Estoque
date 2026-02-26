<?php

namespace Modules\Purchases\Services;

use Illuminate\Support\Facades\DB;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Models\PurchaseOrderItem;
use Modules\Purchases\Models\PurchaseReceiptItem;
use Modules\Purchases\Models\PurchaseReturn;
use Modules\Purchases\Models\PurchaseReturnItem;
use RuntimeException;

class PurchaseReturnService
{
    public function __construct(private DocumentNumberService $numberService)
    {
    }

    /**
     * Create a purchase return with its items.
     *
     * @param array $payload
     * @return \Modules\Purchases\Models\PurchaseReturn
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function createReturn(array $payload): PurchaseReturn
    {
        return DB::transaction(function () use ($payload): PurchaseReturn {
            $return = PurchaseReturn::query()->create([
                'numero' => $this->numberService->generate('DEV'),
                'status' => 'aberta',
                'receipt_id' => $payload['receipt_id'] ?? null,
                'order_id' => $payload['order_id'] ?? null,
                'motivo' => $payload['motivo'],
                'data_devolucao' => $payload['data_devolucao'],
            ]);

            foreach ($payload['items'] as $item) {
                PurchaseReturnItem::query()->create([
                    'return_id' => $return->id,
                    'receipt_item_id' => $item['receipt_item_id'] ?? null,
                    'order_item_id' => $item['order_item_id'] ?? null,
                    'item_id' => $item['item_id'],
                    'quantidade_devolvida' => $item['quantidade_devolvida'],
                    'observacoes' => $item['observacoes'] ?? null,
                ]);
            }

            return $return->load('items');
        });
    }

    /**
     * Confirm a purchase return and rollback received quantities.
     *
     * @param int $returnId
     * @return \Modules\Purchases\Models\PurchaseReturn
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function confirmReturn(int $returnId): PurchaseReturn
    {
        return DB::transaction(function () use ($returnId): PurchaseReturn {
            $return = PurchaseReturn::query()->with(['items', 'order.items'])->findOrFail($returnId);

            if ($return->status !== 'aberta') {
                throw new RuntimeException('Somente devolucoes abertas podem ser confirmadas.');
            }

            $affectedOrderIds = [];

            foreach ($return->items as $item) {
                $orderItem = null;
                $receiptItem = null;

                if ($item->receipt_item_id) {
                    $receiptItem = PurchaseReceiptItem::query()->findOrFail($item->receipt_item_id);
                    $orderItem = $receiptItem->orderItem;

                    $receiptItem->quantidade_recebida = max(
                        0,
                        (float) $receiptItem->quantidade_recebida - (float) $item->quantidade_devolvida
                    );
                    $receiptItem->save();
                }

                if ($item->order_item_id) {
                    $explicitOrderItem = PurchaseOrderItem::query()->findOrFail($item->order_item_id);
                    if ($orderItem && $explicitOrderItem->id !== $orderItem->id) {
                        throw new RuntimeException('Item de devolucao com referencias divergentes.');
                    }
                    $orderItem = $explicitOrderItem;
                }

                if (!$orderItem) {
                    throw new RuntimeException('Item de devolucao sem referencia de pedido.');
                }

                $orderItem->quantidade_recebida = max(
                    0,
                    (float) $orderItem->quantidade_recebida - (float) $item->quantidade_devolvida
                );
                $orderItem->save();

                $affectedOrderIds[] = $orderItem->order_id;
            }

            $affectedOrderIds = array_values(array_unique($affectedOrderIds));

            foreach ($affectedOrderIds as $orderId) {
                $order = PurchaseOrder::query()->find($orderId);
                if ($order) {
                    $this->refreshOrderStatus($order);
                }
            }

            $return->status = 'confirmada';
            $return->save();

            // TODO: Integrar com movimentacao de estoque quando o modulo de estoque estiver preparado.

            return $return->refresh();
        });
    }

    /**
     * Cancel a purchase return.
     *
     * @param int $returnId
     * @return \Modules\Purchases\Models\PurchaseReturn
     * @throws \RuntimeException
     */
    public function cancelReturn(int $returnId): PurchaseReturn
    {
        $return = PurchaseReturn::query()->findOrFail($returnId);

        if ($return->status === 'confirmada') {
            throw new RuntimeException('Devolucoes confirmadas nao podem ser canceladas.');
        }

        $return->status = 'cancelada';
        $return->save();

        return $return->refresh();
    }

    /**
     * Refresh order status based on received quantities.
     *
     * @param \Modules\Purchases\Models\PurchaseOrder $order
     * @return void
     */
    private function refreshOrderStatus(PurchaseOrder $order): void
    {
        if (in_array($order->status, ['cancelado', 'fechado'], true)) {
            return;
        }

        $order->loadMissing('items');

        $hasAnyReceived = $order->items->contains(function ($item): bool {
            return (float) $item->quantidade_recebida > 0;
        });

        if (!$hasAnyReceived) {
            $order->status = 'emitido';
            $order->save();
            return;
        }

        $allReceived = $order->items->every(function ($item): bool {
            return (float) $item->quantidade_recebida >= (float) $item->quantidade_pedida;
        });

        $order->status = $allReceived ? 'recebido' : 'parcialmente_recebido';
        $order->save();
    }
}
