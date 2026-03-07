<?php

namespace Modules\Items\Services;

use Modules\Items\Models\Item;
use Modules\Items\Repositories\ItemRepository;

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
        return $this->repo->update($data, $item->id);
    }

    public function delete(Item $item): void
    {
        $this->repo->delete($item->id);
    }
}
