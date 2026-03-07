<?php

namespace Modules\Purchases\Repositories;

use Modules\Purchases\Models\PurchasePayable;

interface PurchasePayableRepository
{
    /**
     * Get the Datatable query and columns map.
     *
     * @param array $filters
     * @return array
     */
    public function getDatatableQuery(array $filters): array;

    /**
     * Find a payable with related entities.
     *
     * @param int $id
     * @param array $relations
     * @return PurchasePayable
     */
    public function findByIdWithRelations(int $id, array $relations = []): PurchasePayable;
}
