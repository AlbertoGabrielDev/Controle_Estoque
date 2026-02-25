<?php

namespace Modules\Items\Services;

use Modules\Items\Models\Item;

class ItemService
{
    public function create(array $data): Item
    {
        return Item::query()->create($data);
    }

    public function update(Item $item, array $data): Item
    {
        $item->update($data);

        return $item->refresh();
    }

    public function delete(Item $item): void
    {
        $item->delete();
    }
}
