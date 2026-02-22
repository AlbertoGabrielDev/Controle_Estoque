<?php

namespace App\Repositories;

use App\Models\Item;

class ItemRepository
{
    public function __construct(private Item $model)
    {
    }

    public function create(array $data): Item
    {
        return $this->model->create($data);
    }

    public function update(Item $item, array $data): Item
    {
        $item->update($data);
        return $item;
    }

    public function delete(Item $item): void
    {
        $item->delete();
    }
}
