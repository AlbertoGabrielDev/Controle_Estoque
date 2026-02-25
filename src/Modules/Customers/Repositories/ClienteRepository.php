<?php

namespace Modules\Customers\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Modules\Customers\Models\Cliente;
use Prettus\Repository\Contracts\RepositoryInterface;

interface ClienteRepository extends RepositoryInterface
{
    public function paginateWithFilters(array $filters): LengthAwarePaginator;

    public function getSegments(): EloquentCollection;

    public function createCliente(array $data, int $userId): Cliente;

    public function updateCliente(int|string $id, array $data): Cliente;

    public function findWithRelations(int|string $id): Cliente;

    public function deleteCliente(int|string $id): bool;

    public function autocomplete(string $term, int $limit = 20): Collection;

    public function ufs(): array;
}
