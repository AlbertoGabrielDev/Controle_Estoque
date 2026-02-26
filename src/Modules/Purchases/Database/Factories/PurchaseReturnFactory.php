<?php

namespace Modules\Purchases\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Purchases\Models\PurchaseReceipt;
use Modules\Purchases\Models\PurchaseReturn;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Purchases\Models\PurchaseReturn>
 */
class PurchaseReturnFactory extends Factory
{
    protected $model = PurchaseReturn::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero' => 'DEV-' . $this->faker->unique()->numberBetween(1, 999999),
            'status' => 'aberta',
            'receipt_id' => PurchaseReceiptFactory::new(),
            'order_id' => function (array $attributes): ?int {
                $receiptId = $attributes['receipt_id'] ?? null;
                $receipt = $receiptId ? PurchaseReceipt::query()->find($receiptId) : null;

                return $receipt?->order_id;
            },
            'motivo' => $this->faker->sentence(6),
            'data_devolucao' => $this->faker->date(),
        ];
    }
}
