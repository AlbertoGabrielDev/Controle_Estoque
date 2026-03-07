<?php

namespace Modules\Customers\Repositories;

use Modules\Customers\Models\CustomerSegment;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class CustomerSegmentRepositoryEloquent extends BaseRepository implements CustomerSegmentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CustomerSegment::class;
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
        $q = trim((string) ($filters['q'] ?? ''));

        $t = $this->model->getTable();

        $query = $this->model->newQuery()
            ->when($q !== '', function ($query) use ($q, $t) {
                $query->where("{$t}.nome", 'like', "%{$q}%");
            });

        $columnsMap = $this->model::dtColumns();

        return [$query, $columnsMap];
    }
}
