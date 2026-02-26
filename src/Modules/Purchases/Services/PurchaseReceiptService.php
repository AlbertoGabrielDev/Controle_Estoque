<?php

namespace Modules\Purchases\Services;

use Illuminate\Support\Facades\DB;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Models\PurchaseOrderItem;
use Modules\Purchases\Models\PurchasePayable;
use Modules\Purchases\Models\PurchaseReceipt;
use Modules\Purchases\Models\PurchaseReceiptItem;
use RuntimeException;

class PurchaseReceiptService
{
    public function __construct(
        private DocumentNumberService $numberService,
        private AccountsPayableIntegrationService $payableService
    ) {
    }

    /**
     * Register a new receipt for a purchase order.
     *
     * @param array $payload
     * @return \Modules\Purchases\Models\PurchaseReceipt
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function registerReceipt(array $payload): PurchaseReceipt
    {
        return DB::transaction(function () use ($payload): PurchaseReceipt {
            $order = PurchaseOrder::query()->with('items')->findOrFail($payload['order_id']);

            if ($order->status === 'cancelado') {
                throw new RuntimeException('Pedidos cancelados nao podem receber recebimentos.');
            }

            $receipt = PurchaseReceipt::query()->create([
                'numero' => $this->numberService->generate('REC'),
                'status' => 'registrado',
                'order_id' => $order->id,
                'supplier_id' => $order->supplier_id,
                'data_recebimento' => $payload['data_recebimento'],
                'observacoes' => $payload['observacoes'] ?? null,
            ]);

            foreach ($payload['items'] as $item) {
                $orderItem = PurchaseOrderItem::query()->findOrFail($item['order_item_id']);

                if ($orderItem->order_id !== $order->id) {
                    throw new RuntimeException('Item informado nao pertence ao pedido.');
                }

                $quantity = (float) $item['quantidade_recebida'];

                PurchaseReceiptItem::query()->create([
                    'receipt_id' => $receipt->id,
                    'order_item_id' => $orderItem->id,
                    'item_id' => $orderItem->item_id,
                    'quantidade_recebida' => $quantity,
                    'preco_unit_recebido' => $item['preco_unit_recebido'],
                    'imposto_id' => $item['imposto_id'] ?? $orderItem->imposto_id,
                    'aliquota_snapshot' => $item['aliquota_snapshot'] ?? $orderItem->aliquota_snapshot,
                    'divergencia_flag' => false,
                    'motivo_divergencia' => null,
                ]);

                $orderItem->quantidade_recebida = (float) $orderItem->quantidade_recebida + $quantity;
                $orderItem->save();
            }

            $this->refreshOrderStatus($order);

            return $receipt->load('items');
        });
    }

    /**
     * Check a receipt against its order items and mark divergences.
     *
     * @param int $receiptId
     * @return \Modules\Purchases\Models\PurchaseReceipt
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function checkReceipt(int $receiptId): PurchaseReceipt
    {
        return DB::transaction(function () use ($receiptId): PurchaseReceipt {
            $receipt = PurchaseReceipt::query()->with(['items', 'order.items'])->findOrFail($receiptId);

            if ($receipt->status === 'estornado') {
                throw new RuntimeException('Recebimentos estornados nao podem ser conferidos.');
            }

            $hasDivergence = false;

            foreach ($receipt->items as $receiptItem) {
                $orderItem = $receiptItem->orderItem;
                $divergences = [];

                if (!$this->numbersEqual($receiptItem->quantidade_recebida, $orderItem->quantidade_pedida, 3)) {
                    $divergences[] = 'Quantidade divergente';
                }

                if (!$this->numbersEqual($receiptItem->preco_unit_recebido, $orderItem->preco_unit, 2)) {
                    $divergences[] = 'Preco divergente';
                }

                if ((int) $receiptItem->imposto_id !== (int) $orderItem->imposto_id) {
                    $divergences[] = 'Imposto divergente';
                }

                if (!empty($divergences)) {
                    $hasDivergence = true;
                    $receiptItem->update([
                        'divergencia_flag' => true,
                        'motivo_divergencia' => implode('; ', $divergences),
                    ]);
                } else {
                    $receiptItem->update([
                        'divergencia_flag' => false,
                        'motivo_divergencia' => null,
                    ]);
                }
            }

            $receipt->status = $hasDivergence ? 'com_divergencia' : 'conferido';
            $receipt->save();

            if (!$hasDivergence) {
                $this->payableService->createPayableFromReceipt($receipt->id);
            }

            return $receipt->refresh()->load('items');
        });
    }

    /**
     * Accept divergences for a receipt and proceed with payable generation.
     *
     * @param int $receiptId
     * @return \Modules\Purchases\Models\PurchaseReceipt
     * @throws \RuntimeException
     */
    public function acceptDivergence(int $receiptId): PurchaseReceipt
    {
        $receipt = PurchaseReceipt::query()->findOrFail($receiptId);

        if ($receipt->status !== 'com_divergencia') {
            throw new RuntimeException('Recebimento nao possui divergencias pendentes.');
        }

        $receipt->status = 'conferido';
        $receipt->save();

        $this->payableService->createPayableFromReceipt($receipt->id);

        return $receipt->refresh();
    }

    /**
     * Reverse a receipt and rollback received quantities.
     *
     * @param int $receiptId
     * @return \Modules\Purchases\Models\PurchaseReceipt
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function reverseReceipt(int $receiptId): PurchaseReceipt
    {
        return DB::transaction(function () use ($receiptId): PurchaseReceipt {
            $receipt = PurchaseReceipt::query()->with(['items', 'order.items'])->findOrFail($receiptId);

            if ($receipt->status === 'estornado') {
                throw new RuntimeException('Recebimento ja estornado.');
            }

            foreach ($receipt->items as $receiptItem) {
                $orderItem = $receiptItem->orderItem;
                $orderItem->quantidade_recebida = max(
                    0,
                    (float) $orderItem->quantidade_recebida - (float) $receiptItem->quantidade_recebida
                );
                $orderItem->save();
            }

            PurchasePayable::query()
                ->where('receipt_id', $receipt->id)
                ->where('status', '!=', 'pago')
                ->update(['status' => 'cancelado']);

            $receipt->status = 'estornado';
            $receipt->save();

            $this->refreshOrderStatus($receipt->order);

            return $receipt->refresh();
        });
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

        $order->load('items');

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

    /**
     * Compare numeric values using a precision scale.
     *
     * @param mixed $left
     * @param mixed $right
     * @param int $scale
     * @return bool
     */
    private function numbersEqual(mixed $left, mixed $right, int $scale): bool
    {
        if (function_exists('bccomp')) {
            return bccomp((string) $left, (string) $right, $scale) === 0;
        }

        return round((float) $left, $scale) === round((float) $right, $scale);
    }
}
