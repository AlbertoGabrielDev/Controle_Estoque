<?php

namespace Modules\MeasureUnits\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Modules\MeasureUnits\Models\UnidadeMedida;

class UnidadeMedidaRepositoryEloquent extends BaseRepository implements UnidadeMedidaRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UnidadeMedida::class;
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
     * Get base options for select components.
     *
     * @param int|string|null $excludeId ID to exclude to avoid self-referencing.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBaseOptions(int|string|null $excludeId = null)
    {
        return $this->model
            ->newQuery()
            ->when(!is_null($excludeId), fn($q) => $q->where('id', '<>', $excludeId))
            ->select('id', 'codigo', 'descricao')
            ->orderBy('codigo')
            ->get();
    }
}
