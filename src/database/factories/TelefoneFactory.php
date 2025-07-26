<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Telefone>
 */
class TelefoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_fornecedor_fk' => null, // será definido no seeder!
            'telefone' => $this->faker->phoneNumber(),
            'ddd' => $this->faker->numerify('##'),
            'principal' => $this->faker->boolean(),
            'whatsapp' => $this->faker->boolean(),
            'telegram' => $this->faker->boolean(),
            // 'created_at' e 'updated_at' o Laravel preenche sozinho
        ];
    }
}
