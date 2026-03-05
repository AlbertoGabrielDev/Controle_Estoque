<?php

namespace Modules\Purchases\Repositories;

use Illuminate\Support\Collection;
use Modules\Purchases\Models\PurchaseOrder;

interface PurchaseOrderRepository
{
    /**
     * Find an order with all related entities for the Show page.
     *
     * @param int $id
     * @return PurchaseOrder
     */
    public function findWithRelations(int $id): PurchaseOrder;

    /**
     * Get approved requisitions for the order creation dropdown.
     *
     * @return Collection
     */
    public function requisitionsOptions(): Collection;

    /**
     * Get active suppliers for the order creation dropdown.
     *
     * @return Collection
     */
    public function suppliersOptions(): Collection;
}
