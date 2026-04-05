<?php

namespace Modules\Commercial\Repositories;

use Illuminate\Support\Collection;
use Modules\Commercial\Models\CommercialSalesReturn;

interface CommercialSalesReturnRepository
{
    /**
     * Find a return with all related entities for the Show page.
     *
     * @param int $id
     * @return CommercialSalesReturn
     */
    public function findWithRelations(int $id): CommercialSalesReturn;

    /**
     * Find a return with items for the Edit page.
     *
     * @param int $id
     * @return CommercialSalesReturn
     */
    public function findForEdit(int $id): CommercialSalesReturn;

    /**
     * Get invoice items available to be returned for a given invoice.
     *
     * @param int $invoiceId
     * @return Collection
     */
    public function returnableItemsForInvoice(int $invoiceId): Collection;
}
