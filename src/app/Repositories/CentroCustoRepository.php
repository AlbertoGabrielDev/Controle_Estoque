<?php

namespace App\Repositories;

use App\Models\CentroCusto;

class CentroCustoRepository
{
    public function __construct(private CentroCusto $model)
    {
    }

    public function create(array $data): CentroCusto
    {
        return $this->model->create($data);
    }

    public function update(CentroCusto $centroCusto, array $data): CentroCusto
    {
        $centroCusto->update($data);
        return $centroCusto;
    }

    public function delete(CentroCusto $centroCusto): void
    {
        $centroCusto->delete();
    }
}
