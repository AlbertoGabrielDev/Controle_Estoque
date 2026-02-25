<?php

namespace Modules\MeasureUnits\Services;

use Modules\MeasureUnits\Models\UnidadeMedida;

class UnidadeMedidaService
{
    public function create(array $data): UnidadeMedida
    {
        return UnidadeMedida::query()->create($data);
    }

    public function update(UnidadeMedida $unidadeMedida, array $data): UnidadeMedida
    {
        $unidadeMedida->update($data);

        return $unidadeMedida->refresh();
    }

    public function delete(UnidadeMedida $unidadeMedida): void
    {
        $unidadeMedida->delete();
    }
}
