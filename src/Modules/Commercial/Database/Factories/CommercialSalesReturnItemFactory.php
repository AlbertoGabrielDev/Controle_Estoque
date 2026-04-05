<?php

namespace Modules\Commercial\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Commercial\Models\CommercialSalesReturnItem;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Commercial\Models\CommercialSalesReturnItem>
 */
class CommercialSalesReturnItemFactory extends Factory
{
    protected $model = CommercialSalesReturnItem::class;

    public function definition(): array
    {
        return [
            'return_id'            => null,
            'invoice_item_id'      => null,
            'order_item_id'        => null,
            'item_id'              => null,
            'quantidade_devolvida' => $this->faker->randomFloat(3, 1, 5),
            'observacoes'          => $this->faker->optional()->sentence(),
        ];
    }
}
