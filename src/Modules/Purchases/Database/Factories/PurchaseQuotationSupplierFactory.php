<?php

namespace Modules\Purchases\Database\Factories;

use App\Models\Fornecedor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Purchases\Models\PurchaseQuotationSupplier;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Purchases\Models\PurchaseQuotationSupplier>
 */
class PurchaseQuotationSupplierFactory extends Factory
{
    protected $model = PurchaseQuotationSupplier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $suffix = Str::upper(Str::random(6));

        return [
            'quotation_id' => PurchaseQuotationFactory::new(),
            'supplier_id' => function () use ($suffix): int {
                $user = User::factory()->create();

                return Fornecedor::query()->create([
                    'codigo' => 'FOR' . $suffix,
                    'razao_social' => 'Fornecedor ' . $suffix,
                    'nome_fornecedor' => 'Fornecedor ' . $suffix,
                    'cnpj' => '12.345.678/0001-' . random_int(10, 99),
                    'nif_cif' => 'NIF' . $suffix,
                    'cep' => '01001-000',
                    'logradouro' => 'Rua A',
                    'bairro' => 'Centro',
                    'numero_casa' => '123',
                    'cidade' => 'Sao Paulo',
                    'uf' => 'SP',
                    'email' => 'fornecedor_' . strtolower($suffix) . '@example.com',
                    'id_users_fk' => $user->id,
                    'ativo' => true,
                    'status' => 1,
                ])->id_fornecedor;
            },
            'status' => 'convidado',
            'prazo_entrega_dias' => $this->faker->optional()->numberBetween(1, 30),
            'condicao_pagamento' => $this->faker->optional()->sentence(3),
            'observacoes' => $this->faker->optional()->sentence(6),
        ];
    }
}
