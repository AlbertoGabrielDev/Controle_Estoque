<?php

namespace Modules\Purchases\Repositories;

use Illuminate\Http\Request;
use Modules\Purchases\Models\PurchasePayable;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class PurchasePayableRepositoryEloquent extends BaseRepository implements PurchasePayableRepository
{
    public function model(): string
    {
        return PurchasePayable::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
        parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    public function getDatatableQuery(array $filters): array
    {
        $request = new Request($filters);
        return PurchasePayable::makeDatatableQuery($request);
    }

    /**
     * {@inheritdoc}
     */
    public function findByIdWithRelations(int $id, array $relations = []): PurchasePayable
    {
        return PurchasePayable::query()
            ->with($relations)
            ->findOrFail($id);
    }
}
