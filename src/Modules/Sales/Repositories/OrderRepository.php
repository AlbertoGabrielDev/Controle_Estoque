<?php

namespace Modules\Sales\Repositories;

use Modules\Sales\Models\Order;
use Modules\Sales\Models\OrderItem;

interface OrderRepository
{
    public function create(array $attributes): Order;

    public function createItem(array $attributes): OrderItem;
}
