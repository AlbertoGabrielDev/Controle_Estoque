<?php

namespace Modules\Commercial\Repositories;

use Illuminate\Support\Collection;
use Modules\Commercial\Models\CommercialDiscountPolicy;

interface CommercialDiscountPolicyRepository
{
    /**
     * Get all active discount policies as options for dropdowns.
     *
     * @return Collection
     */
    public function activeOptions(): Collection;

    /**
     * Get the maximum allowed discount percentage for a given type.
     *
     * @param string $tipo  'item' or 'pedido'
     * @return float
     */
    public function maxPercentByType(string $tipo): float;
}
