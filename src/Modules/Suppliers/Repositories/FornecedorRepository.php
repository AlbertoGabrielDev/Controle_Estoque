<?php

namespace Modules\Suppliers\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface FornecedorRepository extends RepositoryInterface
{
    /**
     * Get the Datatable query and columns map.
     *
     * @param array $filters
     * @return array{0: \Illuminate\Database\Eloquent\Builder, 1: array}
     */
    public function makeDatatableQuery(array $filters): array;
}
