<?php

namespace Modules\Purchases\Repositories;

use Modules\Purchases\Models\PurchaseReturn;

interface PurchaseReturnRepository
{
    /**
     * Get the Datatable query and columns map.
     *
     * @param array $filters
     * @return array
     */
    public function getDatatableQuery(array $filters): array;

    /**
     * Find a return with related entities.
     *
     * @param int $id
     * @param array $relations
     * @return PurchaseReturn
     */
    public function findByIdWithRelations(int $id, array $relations = []): PurchaseReturn;

    /**
     * Create a new return record.
     *
     * @param array $data
     * @return PurchaseReturn
     */
    public function createReturn(array $data): PurchaseReturn;
}
