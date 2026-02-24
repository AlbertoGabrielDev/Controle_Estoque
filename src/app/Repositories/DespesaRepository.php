<?php

namespace App\Repositories;

use App\Models\Despesa;

class DespesaRepository
{
    public function __construct(private Despesa $model)
    {
    }

    public function create(array $data): Despesa
    {
        return $this->model->create($data);
    }

    public function update(Despesa $despesa, array $data): Despesa
    {
        $despesa->update($data);
        return $despesa;
    }

    public function delete(Despesa $despesa): void
    {
        $despesa->delete();
    }
}
