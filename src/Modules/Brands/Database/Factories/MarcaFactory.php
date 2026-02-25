<?php

namespace Modules\Brands\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Brands\Models\Marca;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Brands\Models\Marca>
 */
class MarcaFactory extends Factory
{
    protected $model = Marca::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $marcas = [
            'Nestlé',
            'Perdigão',
            'Sadia',
            'Italac',
            'Piraquê',
            'Arcor',
            'Bauducco',
            'Três Corações',
            'Aurora',
            'Yoki',
            'Seara',
            'Vigor',
            'Coca-Cola',
            'Pepsico',
            'Danone',
            'Unilever',
            'Kibon',
            'Heinz',
            'Parmalat',
            'Barilla'
        ];

        return [
            'nome_marca' => $this->faker->unique()->randomElement($marcas),
            'status' => $this->faker->boolean(),
            'id_users_fk' => 1,
            // 'created_at' e 'updated_at' o Laravel preenche sozinho
        ];
    }
}
