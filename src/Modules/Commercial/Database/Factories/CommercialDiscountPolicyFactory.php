<?php

namespace Modules\Commercial\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Commercial\Models\CommercialDiscountPolicy;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Commercial\Models\CommercialDiscountPolicy>
 */
class CommercialDiscountPolicyFactory extends Factory
{
    protected $model = CommercialDiscountPolicy::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->words(3, true),
            'tipo' => $this->faker->randomElement(['item', 'pedido']),
            'percentual_maximo' => $this->faker->randomFloat(2, 1, 30),
            'ativo' => true,
        ];
    }
}
