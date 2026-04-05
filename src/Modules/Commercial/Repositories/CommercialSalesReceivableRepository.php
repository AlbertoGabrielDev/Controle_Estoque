<?php

namespace Modules\Commercial\Repositories;

use Illuminate\Support\Collection;
use Modules\Commercial\Models\CommercialSalesReceivable;

interface CommercialSalesReceivableRepository
{
    /**
     * Find a receivable with all related entities for the Show page.
     *
     * @param int $id
     * @return CommercialSalesReceivable
     */
    public function findWithRelations(int $id): CommercialSalesReceivable;

    /**
     * Get open receivables for a given customer.
     *
     * @param int $clienteId
     * @return Collection
     */
    public function openByCliente(int $clienteId): Collection;
}
