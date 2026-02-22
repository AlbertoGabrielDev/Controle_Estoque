<?php

namespace App\Services;

use App\Models\Item;
use App\Repositories\ItemRepository;

class ItemService
{
    public function __construct(private ItemRepository $repo)
    {
    }

    public function create(array $data): Item
    {
        return $this->repo->create($data);
    }

    public function update(Item $item, array $data): Item
    {
        return $this->repo->update($item, $data);
    }

    public function delete(Item $item): void
    {
        $this->repo->delete($item);
    }
}
