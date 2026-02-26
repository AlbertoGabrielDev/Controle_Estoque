<?php

namespace Modules\Purchases\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Purchases\Models\PurchaseDocumentSequence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Purchases\Models\PurchaseDocumentSequence>
 */
class PurchaseDocumentSequenceFactory extends Factory
{
    protected $model = PurchaseDocumentSequence::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['REQ', 'COT', 'PO', 'REC', 'DEV', 'AP'];

        return [
            'type' => $this->faker->unique()->randomElement($types),
            'last_number' => $this->faker->numberBetween(0, 500),
        ];
    }
}
