<?php

namespace Modules\Commercial\Repositories;

use Illuminate\Support\Collection;
use Modules\Commercial\Models\CommercialOpportunity;

interface CommercialOpportunityRepository
{
    /**
     * Find an opportunity with all related entities for the Show page.
     *
     * @param int $id
     * @return CommercialOpportunity
     */
    public function findWithRelations(int $id): CommercialOpportunity;

    /**
     * Find an opportunity with proposals/orders for the Edit page.
     *
     * @param int $id
     * @return CommercialOpportunity
     */
    public function findForEdit(int $id): CommercialOpportunity;

    /**
     * Get open opportunities for dropdown options.
     *
     * @return Collection
     */
    public function openOptions(): Collection;

    /**
     * Get form payload: clientes and users options.
     *
     * @return array<string, Collection>
     */
    public function formPayload(): array;
}
