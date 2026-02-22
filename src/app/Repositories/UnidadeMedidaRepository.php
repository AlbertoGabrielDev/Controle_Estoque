<?php

namespace App\Repositories;

use App\Models\UnidadeMedida;

class UnidadeMedidaRepository
{
    public function __construct(private UnidadeMedida $model)
    {
    }

    public function create(array $data): UnidadeMedida
    {
        return $this->model->create($data);
    }

    public function update(UnidadeMedida $unidadeMedida, array $data): UnidadeMedida
    {
        $unidadeMedida->update($data);
        return $unidadeMedida;
    }

    public function delete(UnidadeMedida $unidadeMedida): void
    {
        $unidadeMedida->delete();
    }
}
