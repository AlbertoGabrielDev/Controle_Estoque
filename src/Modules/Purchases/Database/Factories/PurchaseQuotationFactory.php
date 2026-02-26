<?php

namespace Modules\Purchases\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Purchases\Models\PurchaseQuotation;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Purchases\Models\PurchaseQuotation>
 */
class PurchaseQuotationFactory extends Factory
{
    protected $model = PurchaseQuotation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero' => 'COT-' . $this->faker->unique()->numberBetween(1, 999999),
            'status' => 'aberta',
            'requisition_id' => PurchaseRequisitionFactory::new(),
            'data_limite' => $this->faker->optional()->date(),
            'observacoes' => $this->faker->optional()->sentence(6),
        ];
    }
}
