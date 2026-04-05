<?php

namespace Modules\Commercial\Repositories;

use Illuminate\Support\Collection;
use Modules\Commercial\Models\CommercialSalesInvoiceItem;
use Modules\Commercial\Models\CommercialSalesReturn;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class CommercialSalesReturnRepositoryEloquent extends BaseRepository implements CommercialSalesReturnRepository
{
    public function model()
    {
        return CommercialSalesReturn::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
        parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    public function findWithRelations(int $id): CommercialSalesReturn
    {
        return CommercialSalesReturn::query()
            ->with(['invoice', 'order', 'cliente', 'items.item'])
            ->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findForEdit(int $id): CommercialSalesReturn
    {
        return CommercialSalesReturn::query()
            ->with(['invoice', 'order', 'cliente', 'items'])
            ->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function returnableItemsForInvoice(int $invoiceId): Collection
    {
        return CommercialSalesInvoiceItem::query()
            ->where('invoice_id', $invoiceId)
            ->with('item')
            ->get();
    }
}
