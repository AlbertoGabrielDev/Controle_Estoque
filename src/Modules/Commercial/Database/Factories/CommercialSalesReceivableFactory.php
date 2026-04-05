<?php

namespace Modules\Commercial\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Commercial\Models\CommercialSalesReceivable;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Commercial\Models\CommercialSalesReceivable>
 */
class CommercialSalesReceivableFactory extends Factory
{
    protected $model = CommercialSalesReceivable::class;

    public function definition(): array
    {
        return [
            'numero_documento' => 'AR-' . $this->faker->unique()->numberBetween(1, 999999),
            'invoice_id'       => null,
            'order_id'         => null,
            'cliente_id'       => null,
            'data_emissao'     => now()->format('Y-m-d'),
            'data_vencimento'  => now()->addDays(30)->format('Y-m-d'),
            'valor_total'      => $this->faker->randomFloat(2, 50, 5000),
            'status'           => 'aberto',
        ];
    }

    public function estornado(): static
    {
        return $this->state(['status' => 'estornado']);
    }
}
