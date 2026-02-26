<?php

namespace Modules\Purchases\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Purchases\Models\PurchaseRequisition;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Purchases\Models\PurchaseRequisition>
 */
class PurchaseRequisitionFactory extends Factory
{
    protected $model = PurchaseRequisition::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero' => 'REQ-' . $this->faker->unique()->numberBetween(1, 999999),
            'status' => 'draft',
            'solicitado_por' => User::factory(),
            'observacoes' => $this->faker->optional()->sentence(6),
            'data_requisicao' => $this->faker->optional()->date(),
        ];
    }
}
