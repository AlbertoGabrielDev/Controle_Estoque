<?php

namespace Modules\Commercial\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Commercial\Models\CommercialDocumentSequence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Commercial\Models\CommercialDocumentSequence>
 */
class CommercialDocumentSequenceFactory extends Factory
{
    protected $model = CommercialDocumentSequence::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->unique()->randomElement(['OPP', 'PROP', 'SO', 'INV', 'RET', 'AR']),
            'last_number' => $this->faker->numberBetween(0, 1000),
        ];
    }
}
