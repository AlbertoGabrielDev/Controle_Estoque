<?php

namespace Modules\Finance\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface DespesaRepository extends RepositoryInterface
{
    /**
     * Get the Datatable query and columns map.
     *
     * @param array $filters
     * @return array{0: \Illuminate\Database\Eloquent\Builder, 1: array}
     */
    public function makeDatatableQuery(array $filters): array;
}
