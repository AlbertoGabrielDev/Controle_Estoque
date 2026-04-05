<?php

namespace Modules\Commercial\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Commercial\Models\CommercialSalesInvoiceItem;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Commercial\Models\CommercialSalesInvoiceItem>
 */
class CommercialSalesInvoiceItemFactory extends Factory
{
    protected $model = CommercialSalesInvoiceItem::class;

    public function definition(): array
    {
        $qty   = $this->faker->randomFloat(3, 1, 5);
        $price = $this->faker->randomFloat(2, 10, 500);

        return [
            'invoice_id'          => null,
            'order_item_id'       => null,
            'item_id'             => null,
            'descricao_snapshot'  => $this->faker->words(3, true),
            'quantidade_faturada' => $qty,
            'preco_unit'          => $price,
            'desconto_percent'    => 0,
            'desconto_valor'      => 0,
            'imposto_id'          => null,
            'aliquota_snapshot'   => null,
            'total_linha'         => round($qty * $price, 2),
        ];
    }
}
