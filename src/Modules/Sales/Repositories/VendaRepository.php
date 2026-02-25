<?php

namespace Modules\Sales\Repositories;

use Modules\Sales\Models\Venda;

interface VendaRepository
{
    public function create(array $attributes): Venda;
}
