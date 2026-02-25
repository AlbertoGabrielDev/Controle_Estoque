<?php

namespace Modules\Sales\Repositories;

use Modules\Sales\Models\Order;
use Modules\Sales\Models\OrderItem;

class OrderRepositoryEloquent implements OrderRepository
{
    public function create(array $attributes): Order
    {
        return Order::create($attributes);
    }

    public function createItem(array $attributes): OrderItem
    {
        if (array_key_exists('subtotal_valor', $attributes) && !array_key_exists('sub_valor', $attributes)) {
            $attributes['sub_valor'] = $attributes['subtotal_valor'];
            unset($attributes['subtotal_valor']);
        }

        return OrderItem::create($attributes);
    }
}
