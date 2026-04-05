<?php

namespace Modules\Commercial\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Commercial\Models\CommercialSalesReturn;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Commercial\Models\CommercialSalesReturn>
 */
class CommercialSalesReturnFactory extends Factory
{
    protected $model = CommercialSalesReturn::class;

    public function definition(): array
    {
        return [
            'numero'         => 'RET-' . $this->faker->unique()->numberBetween(1, 999999),
            'invoice_id'     => null,
            'order_id'       => null,
            'cliente_id'     => null,
            'status'         => 'aberta',
            'motivo'         => $this->faker->sentence(),
            'data_devolucao' => now()->format('Y-m-d'),
        ];
    }

    public function confirmada(): static
    {
        return $this->state(['status' => 'confirmada']);
    }
}
