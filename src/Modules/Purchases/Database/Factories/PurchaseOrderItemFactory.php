<?php

namespace Modules\Purchases\Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Purchases\Models\PurchaseOrderItem;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Purchases\Models\PurchaseOrderItem>
 */
class PurchaseOrderItemFactory extends Factory
{
    protected $model = PurchaseOrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(3, 1, 50);
        $price = $this->faker->randomFloat(2, 1, 100);

        return [
            'order_id' => PurchaseOrderFactory::new(),
            'item_id' => function (): int {
                return Item::query()->create([
                    'sku' => 'SKU-' . Str::upper(Str::random(8)),
                    'nome' => 'Item ' . Str::upper(Str::random(6)),
                    'tipo' => 'produto',
                    'preco_base' => 10.5,
                    'custo' => 5.5,
                    'ativo' => true,
                ])->id;
            },
            'descricao_snapshot' => $this->faker->sentence(4),
            'unidade_medida_id' => null,
            'quantidade_pedida' => $quantity,
            'quantidade_recebida' => 0,
            'preco_unit' => $price,
            'imposto_id' => null,
            'aliquota_snapshot' => null,
            'total_linha' => $quantity * $price,
        ];
    }
}
