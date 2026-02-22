<?php

namespace App\Services;

use App\Models\UnidadeMedida;
use App\Repositories\UnidadeMedidaRepository;

class UnidadeMedidaService
{
    public function __construct(private UnidadeMedidaRepository $repo)
    {
    }

    public function create(array $data): UnidadeMedida
    {
        return $this->repo->create($data);
    }

    public function update(UnidadeMedida $unidadeMedida, array $data): UnidadeMedida
    {
        return $this->repo->update($unidadeMedida, $data);
    }

    public function delete(UnidadeMedida $unidadeMedida): void
    {
        $this->repo->delete($unidadeMedida);
    }
}
