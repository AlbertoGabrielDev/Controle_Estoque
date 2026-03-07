<?php

namespace Modules\Purchases\Repositories;

use Illuminate\Http\Request;
use Modules\Purchases\Models\PurchasePayable;
use Modules\Purchases\Models\PurchaseReceipt;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class PurchaseReceiptRepositoryEloquent extends BaseRepository implements PurchaseReceiptRepository
{
    public function model(): string
    {
        return PurchaseReceipt::class;
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
        return PurchaseReceipt::makeDatatableQuery($request);
    }

    /**
     * {@inheritdoc}
     */
    public function findByIdWithRelations(int $id, array $relations = []): PurchaseReceipt
    {
        return PurchaseReceipt::query()
            ->with($relations)
            ->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function createReceipt(array $data): PurchaseReceipt
    {
        return PurchaseReceipt::query()->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function cancelUnpaidPayables(int $receiptId): void
    {
        PurchasePayable::query()
            ->where('receipt_id', $receiptId)
            ->where('status', '!=', 'pago')
            ->update(['status' => 'cancelado']);
    }
}
