<?php

namespace Modules\Purchases\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Modules\Purchases\Models\PurchaseRequisition;
use Modules\Purchases\Models\PurchaseRequisitionItem;
use RuntimeException;

class PurchaseRequisitionService
{
    public function __construct(private DocumentNumberService $numberService)
    {
    }

    /**
     * Create a new purchase requisition with its items.
     *
     * @param array $payload
     * @param int|null $userId
     * @return \Modules\Purchases\Models\PurchaseRequisition
     * @throws \Throwable
     */
    public function createRequisition(array $payload, ?int $userId = null): PurchaseRequisition
    {
        return DB::transaction(function () use ($payload, $userId): PurchaseRequisition {
            $requisition = PurchaseRequisition::query()->create([
                'numero' => $this->numberService->generate('REQ'),
                'status' => 'draft',
                'solicitado_por' => $userId,
                'observacoes' => $payload['observacoes'] ?? null,
                'data_requisicao' => $payload['data_requisicao'] ?? null,
            ]);

            foreach ($payload['items'] as $item) {
                PurchaseRequisitionItem::query()->create([
                    'requisition_id' => $requisition->id,
                    'item_id' => $item['item_id'],
                    'descricao_snapshot' => $item['descricao_snapshot'],
                    'unidade_medida_id' => $item['unidade_medida_id'] ?? null,
                    'quantidade' => $item['quantidade'],
                    'preco_estimado' => $item['preco_estimado'] ?? 0,
                    'imposto_id' => $item['imposto_id'] ?? null,
                    'observacoes' => $item['observacoes'] ?? null,
                ]);
            }

            return $requisition->load('items');
        });
    }

    /**
     * Update a purchase requisition and replace its items.
     *
     * @param int $requisitionId
     * @param array $payload
     * @return \Modules\Purchases\Models\PurchaseRequisition
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function updateRequisition(int $requisitionId, array $payload): PurchaseRequisition
    {
        return DB::transaction(function () use ($requisitionId, $payload): PurchaseRequisition {
            $requisition = PurchaseRequisition::query()->findOrFail($requisitionId);

            if ($requisition->status !== 'draft') {
                throw new RuntimeException('Somente requisicoes em rascunho podem ser editadas.');
            }

            $requisition->update([
                'observacoes' => $payload['observacoes'] ?? null,
                'data_requisicao' => $payload['data_requisicao'] ?? null,
            ]);

            $requisition->items()->delete();

            foreach ($payload['items'] as $item) {
                PurchaseRequisitionItem::query()->create([
                    'requisition_id' => $requisition->id,
                    'item_id' => $item['item_id'],
                    'descricao_snapshot' => $item['descricao_snapshot'],
                    'unidade_medida_id' => $item['unidade_medida_id'] ?? null,
                    'quantidade' => $item['quantidade'],
                    'preco_estimado' => $item['preco_estimado'] ?? 0,
                    'imposto_id' => $item['imposto_id'] ?? null,
                    'observacoes' => $item['observacoes'] ?? null,
                ]);
            }

            return $requisition->load('items');
        });
    }

    /**
     * Approve a purchase requisition.
     *
     * @param int $requisitionId
     * @return \Modules\Purchases\Models\PurchaseRequisition
     * @throws \RuntimeException
     */
    public function approveRequisition(int $requisitionId): PurchaseRequisition
    {
        $requisition = PurchaseRequisition::query()->findOrFail($requisitionId);

        if ($requisition->status !== 'draft') {
            throw new RuntimeException('Somente requisicoes em rascunho podem ser aprovadas.');
        }

        $requisition->status = 'aprovado';
        if (!$requisition->data_requisicao) {
            $requisition->data_requisicao = Carbon::today();
        }
        $requisition->save();

        return $requisition->refresh();
    }

    /**
     * Cancel a purchase requisition.
     *
     * @param int $requisitionId
     * @return \Modules\Purchases\Models\PurchaseRequisition
     * @throws \RuntimeException
     */
    public function cancelRequisition(int $requisitionId): PurchaseRequisition
    {
        $requisition = PurchaseRequisition::query()->findOrFail($requisitionId);

        if ($requisition->status === 'fechado') {
            throw new RuntimeException('Requisicoes fechadas nao podem ser canceladas.');
        }

        $requisition->status = 'cancelado';
        $requisition->save();

        return $requisition->refresh();
    }

    /**
     * Close a purchase requisition.
     *
     * @param int $requisitionId
     * @return \Modules\Purchases\Models\PurchaseRequisition
     * @throws \RuntimeException
     */
    public function closeRequisition(int $requisitionId): PurchaseRequisition
    {
        $requisition = PurchaseRequisition::query()->findOrFail($requisitionId);

        if ($requisition->status !== 'aprovado') {
            throw new RuntimeException('Somente requisicoes aprovadas podem ser fechadas.');
        }

        $requisition->status = 'fechado';
        $requisition->save();

        return $requisition->refresh();
    }
}
