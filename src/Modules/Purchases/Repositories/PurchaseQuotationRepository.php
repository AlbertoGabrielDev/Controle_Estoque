<?php

namespace Modules\Purchases\Repositories;

use Modules\Purchases\Models\PurchaseQuotation;

interface PurchaseQuotationRepository
{
    /**
     * Get the Datatable query and columns map.
     *
     * @param array $filters
     * @return array
     */
    public function getDatatableQuery(array $filters): array;

    /**
     * Find a quotation with related entities.
     *
     * @param int $id
     * @param array $relations
     * @return PurchaseQuotation
     */
    public function findByIdWithRelations(int $id, array $relations = []): PurchaseQuotation;

    /**
     * Create a new quotation record.
     *
     * @param array $data
     * @return PurchaseQuotation
     */
    public function createQuotation(array $data): PurchaseQuotation;
}
