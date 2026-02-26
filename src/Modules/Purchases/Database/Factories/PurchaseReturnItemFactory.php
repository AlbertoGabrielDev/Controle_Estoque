<?php

namespace Modules\Purchases\Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Purchases\Models\PurchaseReceiptItem;
use Modules\Purchases\Models\PurchaseReturnItem;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Purchases\Models\PurchaseReturnItem>
 */
class PurchaseReturnItemFactory extends Factory
{
    protected $model = PurchaseReturnItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'return_id' => PurchaseReturnFactory::new(),
            'receipt_item_id' => PurchaseReceiptItemFactory::new(),
            'order_item_id' => null,
            'item_id' => function (array $attributes): int {
                $receiptItemId = $attributes['receipt_item_id'] ?? null;
                $receiptItem = $receiptItemId ? PurchaseReceiptItem::query()->find($receiptItemId) : null;

                if ($receiptItem) {
                    return $receiptItem->item_id;
                }

                return Item::query()->create([
                    'sku' => 'SKU-' . Str::upper(Str::random(8)),
                    'nome' => 'Item ' . Str::upper(Str::random(6)),
                    'tipo' => 'produto',
                    'preco_base' => 10.5,
                    'custo' => 5.5,
                    'ativo' => true,
                ])->id;
            },
            'quantidade_devolvida' => $this->faker->randomFloat(3, 1, 10),
            'observacoes' => $this->faker->optional()->sentence(5),
        ];
    }
}
