<?php

namespace Modules\Commercial\Repositories;

use Illuminate\Support\Collection;
use Modules\Commercial\Models\CommercialSalesInvoice;
use Modules\Commercial\Models\CommercialSalesOrderItem;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class CommercialSalesInvoiceRepositoryEloquent extends BaseRepository implements CommercialSalesInvoiceRepository
{
    public function model()
    {
        return CommercialSalesInvoice::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
        parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    public function findWithRelations(int $id): CommercialSalesInvoice
    {
        return CommercialSalesInvoice::query()
            ->with(['order', 'cliente', 'items.item', 'receivables', 'returns'])
            ->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findForEdit(int $id): CommercialSalesInvoice
    {
        return CommercialSalesInvoice::query()
            ->with(['order', 'cliente', 'items'])
            ->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function invoiceableItemsForOrder(int $orderId): Collection
    {
        return CommercialSalesOrderItem::query()
            ->where('order_id', $orderId)
            ->whereRaw('quantidade_faturada < quantidade')
            ->with('item')
            ->get();
    }
}
