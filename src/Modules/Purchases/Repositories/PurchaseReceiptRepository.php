<?php

namespace Modules\Purchases\Repositories;

use Modules\Purchases\Models\PurchaseReceipt;

interface PurchaseReceiptRepository
{
    /**
     * Get the Datatable query and columns map.
     *
     * @param array $filters
     * @return array
     */
    public function getDatatableQuery(array $filters): array;

    /**
     * Find a receipt with related entities.
     *
     * @param int $id
     * @param array $relations
     * @return PurchaseReceipt
     */
    public function findByIdWithRelations(int $id, array $relations = []): PurchaseReceipt;

    /**
     * Create a new receipt record.
     *
     * @param array $data
     * @return PurchaseReceipt
     */
    public function createReceipt(array $data): PurchaseReceipt;

    /**
     * Cancel unpaid payables for a given receipt.
     *
     * @param int $receiptId
     * @return void
     */
    public function cancelUnpaidPayables(int $receiptId): void;
}
