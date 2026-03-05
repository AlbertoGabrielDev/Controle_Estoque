<?php

namespace Modules\Purchases\Repositories;

use App\Models\Item;
use App\Models\UnidadeMedida;
use Illuminate\Support\Collection;
use Modules\Purchases\Models\PurchaseRequisition;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class PurchaseRequisitionRepositoryEloquent extends BaseRepository implements PurchaseRequisitionRepository
{
    public function model()
    {
        return PurchaseRequisition::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
        parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    public function findWithRelations(int $id): PurchaseRequisition
    {
        return PurchaseRequisition::query()
            ->with(['items', 'quotations.orders', 'orders'])
            ->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findForEdit(int $id): PurchaseRequisition
    {
        return PurchaseRequisition::query()
            ->with('items')
            ->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function approvedOptions(): Collection
    {
        return PurchaseRequisition::query()
            ->select('id', 'numero', 'data_requisicao', 'observacoes', 'status')
            ->where('status', 'LIKE', '%aprovado%')
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function formPayload(): array
    {
        return [
            'items_options' => Item::query()
                ->select('id', 'sku', 'nome', 'descricao', 'unidade_medida_id')
                ->where('ativo', true)
                ->get(),
            'unidades_options' => UnidadeMedida::query()
                ->select('id', 'codigo', 'descricao')
                ->where('ativo', true)
                ->get(),
        ];
    }
}
