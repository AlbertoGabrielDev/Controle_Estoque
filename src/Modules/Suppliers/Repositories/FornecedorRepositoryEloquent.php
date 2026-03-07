<?php

namespace Modules\Suppliers\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Modules\Suppliers\Models\Fornecedor;

class FornecedorRepositoryEloquent extends BaseRepository implements FornecedorRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Fornecedor::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Get the Datatable query and columns map.
     *
     * @param array $filters
     * @return array{0: \Illuminate\Database\Eloquent\Builder, 1: array}
     */
    public function makeDatatableQuery(array $filters): array
    {
        $request = new \Illuminate\Http\Request($filters);
        return $this->model::makeDatatableQuery($request);
    }
}
