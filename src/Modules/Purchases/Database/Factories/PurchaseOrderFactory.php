<?php

namespace Modules\Purchases\Database\Factories;

use App\Models\Fornecedor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Purchases\Models\PurchaseOrder;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Purchases\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $suffix = Str::upper(Str::random(6));

        return [
            'numero' => 'PO-' . $this->faker->unique()->numberBetween(1, 999999),
            'status' => 'emitido',
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
            'quotation_id' => PurchaseQuotationFactory::new(),
            'data_emissao' => $this->faker->date(),
            'data_prevista' => $this->faker->optional()->date(),
            'observacoes' => $this->faker->optional()->sentence(6),
            'total' => 0,
        ];
    }
}
