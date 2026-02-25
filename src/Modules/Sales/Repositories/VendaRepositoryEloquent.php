<?php

namespace Modules\Sales\Repositories;

use Modules\Sales\Models\Venda;

class VendaRepositoryEloquent implements VendaRepository
{
    public function create(array $attributes): Venda
    {
        return Venda::create($attributes);
    }
}
