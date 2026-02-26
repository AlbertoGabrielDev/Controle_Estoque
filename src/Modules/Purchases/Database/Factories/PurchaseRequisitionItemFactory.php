<?php

namespace Modules\Purchases\Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Purchases\Models\PurchaseRequisitionItem;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Purchases\Models\PurchaseRequisitionItem>
 */
class PurchaseRequisitionItemFactory extends Factory
{
    protected $model = PurchaseRequisitionItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requisition_id' => PurchaseRequisitionFactory::new(),
            'item_id' => function () {
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
            'quantidade' => $this->faker->randomFloat(3, 1, 50),
            'preco_estimado' => $this->faker->randomFloat(2, 1, 100),
            'imposto_id' => null,
            'observacoes' => $this->faker->optional()->sentence(5),
        ];
    }
}
