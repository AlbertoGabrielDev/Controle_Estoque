<?php

namespace Modules\Commercial\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Commercial\Models\CommercialSalesReturn;
use Modules\Commercial\Models\CommercialSalesReturnItem;
use RuntimeException;

class SalesReturnService
{
    public function __construct(
        private CommercialDocumentNumberService $numberService,
        private AccountsReceivableIntegrationService $arService,
    ) {
    }

    /**
     * Create a new return (devolution) in open status.
     *
     * @param array $payload  Required: cliente_id, motivo, data_devolucao, items[].
     *                        Optional: invoice_id, order_id.
     * @return CommercialSalesReturn
     * @throws \Throwable
     */
    public function createReturn(array $payload): CommercialSalesReturn
    {
        return DB::transaction(function () use ($payload): CommercialSalesReturn {
            $return = CommercialSalesReturn::query()->create([
                'numero'          => $this->numberService->generate('RET'),
                'invoice_id'      => $payload['invoice_id'] ?? null,
                'order_id'        => $payload['order_id'] ?? null,
                'cliente_id'      => $payload['cliente_id'],
                'status'          => 'aberta',
                'motivo'          => $payload['motivo'],
                'data_devolucao'  => $payload['data_devolucao'] ?? Carbon::today(),
            ]);

            foreach ($payload['items'] ?? [] as $item) {
                CommercialSalesReturnItem::query()->create([
                    'return_id'           => $return->id,
                    'invoice_item_id'     => $item['invoice_item_id'] ?? null,
                    'order_item_id'       => $item['order_item_id'] ?? null,
                    'item_id'             => $item['item_id'],
                    'quantidade_devolvida'=> $item['quantidade_devolvida'],
                    'observacoes'         => $item['observacoes'] ?? null,
                ]);
            }

            return $return->load('items');
        });
    }

    /**
     * Confirm a return: adjust invoiced quantities and reverse the receivable.
     *
     * TODO: When stock integration is implemented, add an entry in estoque_movimentos
     * to register the inbound movement for each returned item.
     *
     * @param int $returnId
     * @return CommercialSalesReturn
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function confirmReturn(int $returnId): CommercialSalesReturn
    {
        return DB::transaction(function () use ($returnId): CommercialSalesReturn {
            $return = CommercialSalesReturn::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($returnId);

            if ($return->status !== 'aberta') {
                throw new RuntimeException('Apenas devoluções abertas podem ser confirmadas.');
            }

            // Adjust quantidade_faturada on order items when reference is available
            foreach ($return->items as $returnItem) {
                if ($returnItem->order_item_id) {
                    \Modules\Commercial\Models\CommercialSalesOrderItem::query()
                        ->where('id', $returnItem->order_item_id)
                        ->decrement('quantidade_faturada', (float) $returnItem->quantidade_devolvida);
                }
            }

            $return->update(['status' => 'confirmada']);

            // Reverse associated open receivables from the invoice
            if ($return->invoice_id) {
                $receivables = \Modules\Commercial\Models\CommercialSalesReceivable::query()
                    ->where('invoice_id', $return->invoice_id)
                    ->where('status', 'aberto')
                    ->get();

                $this->arService->reverseReceivableFromReturn($receivables);
            }

            return $return->fresh();
        });
    }

    /**
     * Cancel an open return.
     *
     * @param int $returnId
     * @return CommercialSalesReturn
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function cancelReturn(int $returnId): CommercialSalesReturn
    {
        return DB::transaction(function () use ($returnId): CommercialSalesReturn {
            $return = CommercialSalesReturn::query()->findOrFail($returnId);

            if ($return->status !== 'aberta') {
                throw new RuntimeException('Apenas devoluções abertas podem ser canceladas.');
            }

            $return->update(['status' => 'cancelada']);

            return $return->fresh();
        });
    }
}
