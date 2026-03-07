<?php

namespace Modules\MeasureUnits\Services;

use Modules\MeasureUnits\Models\UnidadeMedida;
use Modules\MeasureUnits\Repositories\UnidadeMedidaRepository;

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
        return $this->repo->update($data, $unidadeMedida->id);
    }

    public function delete(UnidadeMedida $unidadeMedida): void
    {
        $this->repo->delete($unidadeMedida->id);
    }
}
