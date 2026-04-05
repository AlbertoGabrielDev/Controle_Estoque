<?php

namespace Modules\Commercial\Repositories;

use Illuminate\Support\Collection;
use Modules\Commercial\Models\CommercialSalesInvoice;

interface CommercialSalesInvoiceRepository
{
    /**
     * Find an invoice with all related entities for the Show page.
     *
     * @param int $id
     * @return CommercialSalesInvoice
     */
    public function findWithRelations(int $id): CommercialSalesInvoice;

    /**
     * Find an invoice with items for the Edit page.
     *
     * @param int $id
     * @return CommercialSalesInvoice
     */
    public function findForEdit(int $id): CommercialSalesInvoice;

    /**
     * Get unfilled or partially filled order items available for invoicing.
     *
     * @param int $orderId
     * @return Collection
     */
    public function invoiceableItemsForOrder(int $orderId): Collection;
}
