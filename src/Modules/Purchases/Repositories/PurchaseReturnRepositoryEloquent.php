<?php

namespace Modules\Purchases\Repositories;

use Illuminate\Http\Request;
use Modules\Purchases\Models\PurchaseReturn;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class PurchaseReturnRepositoryEloquent extends BaseRepository implements PurchaseReturnRepository
{
    public function model(): string
    {
        return PurchaseReturn::class;
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
        return PurchaseReturn::makeDatatableQuery($request);
    }

    /**
     * {@inheritdoc}
     */
    public function findByIdWithRelations(int $id, array $relations = []): PurchaseReturn
    {
        return PurchaseReturn::query()
            ->with($relations)
            ->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function createReturn(array $data): PurchaseReturn
    {
        return PurchaseReturn::query()->create($data);
    }
}
