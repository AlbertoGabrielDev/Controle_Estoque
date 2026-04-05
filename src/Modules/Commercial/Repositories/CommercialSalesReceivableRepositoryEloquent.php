<?php

namespace Modules\Commercial\Repositories;

use Illuminate\Support\Collection;
use Modules\Commercial\Models\CommercialSalesReceivable;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class CommercialSalesReceivableRepositoryEloquent extends BaseRepository implements CommercialSalesReceivableRepository
{
    public function model()
    {
        return CommercialSalesReceivable::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
        parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    public function findWithRelations(int $id): CommercialSalesReceivable
    {
        return CommercialSalesReceivable::query()
            ->with(['invoice', 'order', 'cliente'])
            ->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function openByCliente(int $clienteId): Collection
    {
        return CommercialSalesReceivable::query()
            ->where('cliente_id', $clienteId)
            ->where('status', 'aberto')
            ->orderBy('data_vencimento')
            ->get();
    }
}
