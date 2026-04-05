<?php

namespace Modules\Commercial\Repositories;

use Illuminate\Support\Collection;
use Modules\Commercial\Models\CommercialProposal;

interface CommercialProposalRepository
{
    /**
     * Find a proposal with all related entities for the Show page.
     *
     * @param int $id
     * @return CommercialProposal
     */
    public function findWithRelations(int $id): CommercialProposal;

    /**
     * Find a proposal with items for the Edit page.
     *
     * @param int $id
     * @return CommercialProposal
     */
    public function findForEdit(int $id): CommercialProposal;

    /**
     * Get approved proposals available to convert to sales orders.
     *
     * @return Collection
     */
    public function approvedOptions(): Collection;

    /**
     * Get form payload: clientes, items, units, taxes, discount policies.
     *
     * @return array<string, Collection>
     */
    public function formPayload(): array;
}
