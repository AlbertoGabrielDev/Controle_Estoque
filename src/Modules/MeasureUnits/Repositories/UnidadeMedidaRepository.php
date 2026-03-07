<?php

namespace Modules\MeasureUnits\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface UnidadeMedidaRepository extends RepositoryInterface
{
    /**
     * Get the Datatable query and columns map.
     *
     * @param array $filters
     * @return array{0: \Illuminate\Database\Eloquent\Builder, 1: array}
     */
    public function makeDatatableQuery(array $filters): array;

    /**
     * Get base options for select components.
     *
     * @param int|string|null $excludeId ID to exclude to avoid self-referencing.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBaseOptions(int|string|null $excludeId = null);
}
