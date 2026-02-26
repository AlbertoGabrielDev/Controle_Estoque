<?php

namespace Modules\Purchases\Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Purchases\Models\PurchaseQuotationSupplierItem;
use Modules\Purchases\Models\PurchaseRequisitionItem;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Purchases\Models\PurchaseQuotationSupplierItem>
 */
class PurchaseQuotationSupplierItemFactory extends Factory
{
    protected $model = PurchaseQuotationSupplierItem::class;

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
            'quotation_supplier_id' => PurchaseQuotationSupplierFactory::new(),
            'requisition_item_id' => PurchaseRequisitionItemFactory::new(),
            'item_id' => function (array $attributes): int {
                $requisitionItemId = $attributes['requisition_item_id'] ?? null;
                $requisitionItem = $requisitionItemId
                    ? PurchaseRequisitionItem::query()->find($requisitionItemId)
                    : null;

                if ($requisitionItem) {
                    return $requisitionItem->item_id;
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
            'quantidade' => $quantity,
            'preco_unit' => $price,
            'imposto_id' => null,
            'aliquota_snapshot' => null,
            'selecionado' => false,
        ];
    }
}
