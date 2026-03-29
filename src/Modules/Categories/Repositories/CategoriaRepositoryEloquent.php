<?php

namespace Modules\Categories\Repositories;

use Modules\Categories\Models\Categoria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class CategoriaRepositoryEloquent extends BaseRepository implements CategoriaRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Categoria::class;
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
        [$query, $columnsMap] = $this->model::makeDatatableQuery($request);

        $query->with('categoriaPai');

        return [$query, $columnsMap];
    }

    /**
     * List categories with children count for the home page.
     *
     * @param bool $canViewInactive
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listForHome(bool $canViewInactive)
    {
        $query = $this->model->newQuery()->withCount('produtos');

        if (!$canViewInactive) {
            $query->where('ativo', 1);
        }

        return $query
            ->orderBy('nome_categoria')
            ->get(['id_categoria', 'nome_categoria', 'imagem']);
    }

    /**
     * List parent categories options.
     *
     * @param int|null $exceptCategoriaId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listParentOptions(?int $exceptCategoriaId = null)
    {
        $query = $this->model->newQuery()
            ->select('id_categoria', 'nome_categoria')
            ->orderBy('nome_categoria');

        if ($exceptCategoriaId) {
            $query->where('id_categoria', '<>', $exceptCategoriaId);
        }

        return $query->get();
    }

    /**
     * Find category with related counts.
     *
     * @param int $id
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|static|static[]
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFailWithCount(int $id, array $relations)
    {
        return $this->model->newQuery()
            ->withCount($relations)
            ->findOrFail($id);
    }
}
