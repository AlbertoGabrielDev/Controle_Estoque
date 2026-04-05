<?php

namespace Modules\Commercial\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Commercial\Models\CommercialProposalItem;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Commercial\Models\CommercialProposalItem>
 */
class CommercialProposalItemFactory extends Factory
{
    protected $model = CommercialProposalItem::class;

    public function definition(): array
    {
        $qty   = $this->faker->randomFloat(3, 1, 10);
        $price = $this->faker->randomFloat(2, 10, 500);
        $total = round($qty * $price, 2);

        return [
            'proposal_id'        => null,
            'item_id'            => null,
            'descricao_snapshot' => $this->faker->words(3, true),
            'unidade_medida_id'  => null,
            'quantidade'         => $qty,
            'preco_unit'         => $price,
            'desconto_percent'   => 0,
            'desconto_valor'     => 0,
            'imposto_id'         => null,
            'aliquota_snapshot'  => null,
            'total_linha'        => $total,
        ];
    }
}
