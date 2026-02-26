<?php

namespace Modules\Purchases\Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Purchases\Models\PurchaseOrderItem;
use Modules\Purchases\Models\PurchaseReceipt;
use Modules\Purchases\Models\PurchaseReceiptItem;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Purchases\Models\PurchaseReceiptItem>
 */
class PurchaseReceiptItemFactory extends Factory
{
    protected $model = PurchaseReceiptItem::class;

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
            'receipt_id' => PurchaseReceiptFactory::new(),
            'order_item_id' => function (array $attributes): int {
                $receiptId = $attributes['receipt_id'] ?? null;
                $receipt = $receiptId ? PurchaseReceipt::query()->find($receiptId) : null;

                if ($receipt) {
                    $existing = PurchaseOrderItem::query()
                        ->where('order_id', $receipt->order_id)
                        ->first();

                    if ($existing) {
                        return $existing->id;
                    }

                    return PurchaseOrderItemFactory::new()->create([
                        'order_id' => $receipt->order_id,
                    ])->id;
                }

                return PurchaseOrderItemFactory::new()->create()->id;
            },
            'item_id' => function (array $attributes): int {
                $orderItemId = $attributes['order_item_id'] ?? null;
                $orderItem = $orderItemId ? PurchaseOrderItem::query()->find($orderItemId) : null;

                if ($orderItem) {
                    return $orderItem->item_id;
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
            'quantidade_recebida' => $quantity,
            'preco_unit_recebido' => $price,
            'imposto_id' => null,
            'aliquota_snapshot' => null,
            'divergencia_flag' => false,
            'motivo_divergencia' => null,
        ];
    }
}
