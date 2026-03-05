<?php

namespace Modules\Purchases\Repositories;

use Illuminate\Support\Collection;
use Modules\Purchases\Models\PurchaseRequisition;

interface PurchaseRequisitionRepository
{
    /**
     * Find a requisition with all related entities for the Show page.
     *
     * @param int $id
     * @return PurchaseRequisition
     */
    public function findWithRelations(int $id): PurchaseRequisition;

    /**
     * Find a requisition with items for the Edit page.
     *
     * @param int $id
     * @return PurchaseRequisition
     */
    public function findForEdit(int $id): PurchaseRequisition;

    /**
     * Get approved requisitions for dropdown options.
     *
     * @return Collection
     */
    public function approvedOptions(): Collection;

    /**
     * Get items and units options for requisition forms.
     *
     * @return array<string, Collection>
     */
    public function formPayload(): array;
}
