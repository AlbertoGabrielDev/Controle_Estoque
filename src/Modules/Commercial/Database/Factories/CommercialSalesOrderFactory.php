<?php

namespace Modules\Commercial\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Commercial\Models\CommercialSalesOrder;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Commercial\Models\CommercialSalesOrder>
 */
class CommercialSalesOrderFactory extends Factory
{
    protected $model = CommercialSalesOrder::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 100, 10000);

        return [
            'numero'         => 'SO-' . $this->faker->unique()->numberBetween(1, 999999),
            'proposal_id'    => null,
            'opportunity_id' => null,
            'cliente_id'     => null,
            'status'         => 'rascunho',
            'data_pedido'    => now()->format('Y-m-d'),
            'observacoes'    => $this->faker->optional()->sentence(),
            'subtotal'       => $subtotal,
            'desconto_total' => 0,
            'total_impostos' => 0,
            'total'          => $subtotal,
        ];
    }

    public function confirmado(): static
    {
        return $this->state(['status' => 'confirmado']);
    }

    public function cancelado(): static
    {
        return $this->state(['status' => 'cancelado']);
    }
}
