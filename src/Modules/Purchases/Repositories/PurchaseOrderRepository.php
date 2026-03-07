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
     * Find an order with its items.
     *
     * @param int $id
     * @return PurchaseOrder
     */
    public function findByIdWithItems(int $id): PurchaseOrder;

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

    /**
     * Get orders available for receipt creation (emitido or parcialmente_recebido).
     *
     * @return Collection
     */
    public function getAvailableForReceipt(): Collection;
}
