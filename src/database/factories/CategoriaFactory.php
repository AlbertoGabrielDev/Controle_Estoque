<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Categoria>
 */
class CategoriaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categorias = [
            'Cereais',
            'Leguminosas',
            'Massas e Macarrão',
            'Cafés e Bebidas Quentes',
            'Laticínios',
            'Óleos e Gorduras',
            'Açúcares e Doces',
            'Farinhas e Misturas',
            'Temperos e Condimentos',
            'Molhos e Conservas',
            'Biscoitos e Snacks',
            'Carnes e Aves',
            'Peixes e Frutos do Mar',
            'Hortifruti',
            'Padaria',
            'Bebidas Não Alcoólicas',
            'Bebidas Alcoólicas',
            'Congelados',
            'Produtos de Limpeza',
            'Higiene Pessoal',
            'Pet Shop',
            'Bebês',
            'Matinais',
            'Suplementos',
            'Produtos Naturais'
        ];

        return [
            'nome_categoria' => $this->faker->unique()->randomElement($categorias),
            'imagem' => $this->faker->imageUrl(400, 300, 'food', true),
            'status' => $this->faker->boolean(),
            'id_users_fk' => 1,
        ];
    }
}
