<?php

namespace Modules\Categories\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface CategoriaRepository extends RepositoryInterface
{
    /**
     * Get the Datatable query and columns map.
     *
     * @param array $filters
     * @return array{0: \Illuminate\Database\Eloquent\Builder, 1: array}
     */
    public function makeDatatableQuery(array $filters): array;

    /**
     * List categories with children count for the home page.
     *
     * @param bool $canViewInactive
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listForHome(bool $canViewInactive);

    /**
     * List parent categories options.
     *
     * @param int|null $exceptCategoriaId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listParentOptions(?int $exceptCategoriaId = null);

    /**
     * Find category with related counts.
     *
     * @param int $id
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|static|static[]
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFailWithCount(int $id, array $relations);
}
