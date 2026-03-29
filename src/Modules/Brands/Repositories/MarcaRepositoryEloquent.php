<?php

namespace Modules\Brands\Repositories;

use Modules\Brands\Models\Marca;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class MarcaRepositoryEloquent extends BaseRepository implements MarcaRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Marca::class;
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
        $request = \Illuminate\Http\Request::create('/', 'GET', $filters);
        return $this->model::makeDatatableQuery($request);
    }
}
