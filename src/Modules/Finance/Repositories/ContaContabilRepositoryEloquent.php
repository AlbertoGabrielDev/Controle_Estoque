<?php

namespace Modules\Finance\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Modules\Finance\Models\ContaContabil;

class ContaContabilRepositoryEloquent extends BaseRepository implements ContaContabilRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ContaContabil::class;
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

    /**
     * Get parent account options for select components.
     *
     * @param int|string|null $excludeId ID to exclude to avoid self-referencing.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getParentOptions(int|string|null $excludeId = null)
    {
        return $this->model
            ->newQuery()
            ->when(!is_null($excludeId), fn($q) => $q->where('id', '<>', $excludeId))
            ->select('id', 'codigo', 'nome')
            ->orderBy('nome')
            ->get();
    }
}
