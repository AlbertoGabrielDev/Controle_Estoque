<?php

namespace Modules\Commercial\Repositories;

use Illuminate\Support\Collection;
use Modules\Commercial\Models\CommercialSalesOrder;

interface CommercialSalesOrderRepository
{
    /**
     * Find a sales order with all related entities for the Show page.
     *
     * @param int $id
     * @return CommercialSalesOrder
     */
    public function findWithRelations(int $id): CommercialSalesOrder;

    /**
     * Find a sales order with items for the Edit page.
     *
     * @param int $id
     * @return CommercialSalesOrder
     */
    public function findForEdit(int $id): CommercialSalesOrder;

    /**
     * Get confirmed orders available for invoicing.
     *
     * @return Collection
     */
    public function invoiceableOptions(): Collection;

    /**
     * Get form payload: clientes, items, units, taxes, discount policies.
     *
     * @return array<string, Collection>
     */
    public function formPayload(): array;
}
