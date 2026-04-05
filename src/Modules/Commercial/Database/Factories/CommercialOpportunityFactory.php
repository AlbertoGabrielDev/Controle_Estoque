<?php

namespace Modules\Commercial\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Commercial\Models\CommercialOpportunity;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Commercial\Models\CommercialOpportunity>
 */
class CommercialOpportunityFactory extends Factory
{
    protected $model = CommercialOpportunity::class;

    public function definition(): array
    {
        return [
            'codigo'                   => 'OPP-' . $this->faker->unique()->numberBetween(1, 999999),
            'cliente_id'               => null,
            'nome'                     => $this->faker->words(4, true),
            'descricao'                => $this->faker->optional()->sentence(8),
            'origem'                   => $this->faker->optional()->randomElement(['Inbound', 'Referral', 'Cold Call', 'Event']),
            'responsavel_id'           => null,
            'status'                   => 'novo',
            'valor_estimado'           => $this->faker->randomFloat(2, 100, 50000),
            'data_prevista_fechamento' => $this->faker->optional()->dateTimeBetween('now', '+6 months')?->format('Y-m-d'),
            'motivo_perda'             => null,
            'observacoes'              => $this->faker->optional()->sentence(),
        ];
    }

    public function ganho(): static
    {
        return $this->state(['status' => 'ganho']);
    }

    public function perdido(): static
    {
        return $this->state(['status' => 'perdido', 'motivo_perda' => $this->faker->sentence()]);
    }
}
