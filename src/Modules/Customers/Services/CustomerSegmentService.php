<?php

namespace Modules\Customers\Services;

use Modules\Customers\Models\CustomerSegment;
use Modules\Customers\Repositories\CustomerSegmentRepository;

class CustomerSegmentService
{
    public function __construct(private CustomerSegmentRepository $repository)
    {
    }

    public function create(array $data): CustomerSegment
    {
        return $this->repository->create($data);
    }

    public function findOrFail(int|string $id): CustomerSegment
    {
        return $this->repository->find($id);
    }

    public function update(int|string $id, array $data): CustomerSegment
    {
        $segment = $this->findOrFail($id);

        return $this->repository->update($data, $id);
    }

    public function delete(int|string $id): bool
    {
        return (bool) $this->repository->delete($id);
    }
}
