<?php

namespace Modules\Commercial\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Commercial\Models\CommercialProposal;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Commercial\Models\CommercialProposal>
 */
class CommercialProposalFactory extends Factory
{
    protected $model = CommercialProposal::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 100, 10000);

        return [
            'numero'         => 'PROP-' . $this->faker->unique()->numberBetween(1, 999999),
            'opportunity_id' => null,
            'cliente_id'     => null,
            'status'         => 'rascunho',
            'data_emissao'   => now()->format('Y-m-d'),
            'validade_ate'   => now()->addDays(30)->format('Y-m-d'),
            'observacoes'    => $this->faker->optional()->sentence(),
            'subtotal'       => $subtotal,
            'desconto_total' => 0,
            'total_impostos' => 0,
            'total'          => $subtotal,
        ];
    }

    public function aprovada(): static
    {
        return $this->state(['status' => 'aprovada']);
    }

    public function enviada(): static
    {
        return $this->state(['status' => 'enviada']);
    }
}
