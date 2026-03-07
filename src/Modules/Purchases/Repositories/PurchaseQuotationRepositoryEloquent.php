<?php

namespace Modules\Purchases\Repositories;

use Illuminate\Http\Request;
use Modules\Purchases\Models\PurchaseQuotation;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class PurchaseQuotationRepositoryEloquent extends BaseRepository implements PurchaseQuotationRepository
{
    public function model(): string
    {
        return PurchaseQuotation::class;
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
        return PurchaseQuotation::makeDatatableQuery($request);
    }

    /**
     * {@inheritdoc}
     */
    public function findByIdWithRelations(int $id, array $relations = []): PurchaseQuotation
    {
        return PurchaseQuotation::query()
            ->with($relations)
            ->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function createQuotation(array $data): PurchaseQuotation
    {
        return PurchaseQuotation::query()->create($data);
    }
}
