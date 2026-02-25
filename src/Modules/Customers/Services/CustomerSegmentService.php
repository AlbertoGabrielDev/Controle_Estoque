<?php

namespace Modules\Customers\Services;

use Modules\Customers\Models\CustomerSegment;

class CustomerSegmentService
{
    public function create(array $data): CustomerSegment
    {
        return CustomerSegment::query()->create($data);
    }

    public function findOrFail(int|string $id): CustomerSegment
    {
        return CustomerSegment::query()->findOrFail($id);
    }

    public function update(int|string $id, array $data): CustomerSegment
    {
        $segment = $this->findOrFail($id);
        $segment->update($data);

        return $segment->refresh();
    }

    public function delete(int|string $id): bool
    {
        return (bool) $this->findOrFail($id)->delete();
    }
}
