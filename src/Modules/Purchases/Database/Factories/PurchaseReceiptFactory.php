<?php

namespace Modules\Purchases\Database\Factories;

use App\Models\Fornecedor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Models\PurchaseReceipt;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Purchases\Models\PurchaseReceipt>
 */
class PurchaseReceiptFactory extends Factory
{
    protected $model = PurchaseReceipt::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $suffix = Str::upper(Str::random(6));

        return [
            'numero' => 'REC-' . $this->faker->unique()->numberBetween(1, 999999),
            'status' => 'registrado',
            'order_id' => PurchaseOrderFactory::new(),
            'supplier_id' => function (array $attributes) use ($suffix): int {
                $orderId = $attributes['order_id'] ?? null;
                $order = $orderId ? PurchaseOrder::query()->find($orderId) : null;

                if ($order) {
                    return $order->supplier_id;
                }

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
            'data_recebimento' => $this->faker->date(),
            'observacoes' => $this->faker->optional()->sentence(6),
        ];
    }
}
