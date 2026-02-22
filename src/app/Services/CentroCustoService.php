<?php

namespace App\Services;

use App\Models\CentroCusto;
use App\Repositories\CentroCustoRepository;

class CentroCustoService
{
    public function __construct(private CentroCustoRepository $repo)
    {
    }

    public function create(array $data): CentroCusto
    {
        return $this->repo->create($data);
    }

    public function update(CentroCusto $centroCusto, array $data): CentroCusto
    {
        return $this->repo->update($centroCusto, $data);
    }

    public function delete(CentroCusto $centroCusto): void
    {
        $this->repo->delete($centroCusto);
    }
}
