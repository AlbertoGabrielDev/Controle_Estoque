<?php

namespace Modules\Categories\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Categories\Models\Categoria;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Categories\Models\Categoria>
 */
class CategoriaFactory extends Factory
{
    protected $model = Categoria::class;

    public function definition(): array
    {
        $categorias = [
            'Cereais',
            'Leguminosas',
            'Massas e Macarrao',
            'Cafes e Bebidas Quentes',
            'Laticinios',
            'Oleos e Gorduras',
            'Acucares e Doces',
            'Farinhas e Misturas',
            'Temperos e Condimentos',
            'Molhos e Conservas',
            'Biscoitos e Snacks',
            'Carnes e Aves',
            'Peixes e Frutos do Mar',
            'Hortifruti',
            'Padaria',
            'Bebidas Nao Alcoolicas',
            'Bebidas Alcoolicas',
            'Congelados',
            'Produtos de Limpeza',
            'Higiene Pessoal',
            'Pet Shop',
            'Bebes',
            'Matinais',
            'Suplementos',
            'Produtos Naturais',
        ];

        return [
            'nome_categoria' => $this->faker->unique()->randomElement($categorias),
            'imagem' => $this->faker->imageUrl(400, 300, 'food', true),
            'status' => $this->faker->boolean(),
            'id_users_fk' => 1,
        ];
    }
}
