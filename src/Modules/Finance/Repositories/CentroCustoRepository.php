<?php

namespace Modules\Finance\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface CentroCustoRepository extends RepositoryInterface
{
    /**
     * Get the Datatable query and columns map.
     *
     * @param array $filters
     * @return array{0: \Illuminate\Database\Eloquent\Builder, 1: array}
     */
    public function makeDatatableQuery(array $filters): array;

    /**
     * Get parent center options for select components.
     *
     * @param int|string|null $excludeId ID to exclude to avoid self-referencing.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getParentOptions(int|string|null $excludeId = null);
}
