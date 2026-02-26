<?php

namespace Modules\Purchases\Database\Factories;

use App\Models\Fornecedor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Purchases\Models\PurchasePayable;
use Modules\Purchases\Models\PurchaseReceipt;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Purchases\Models\PurchasePayable>
 */
class PurchasePayableFactory extends Factory
{
    protected $model = PurchasePayable::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $suffix = Str::upper(Str::random(6));

        return [
            'receipt_id' => PurchaseReceiptFactory::new(),
            'supplier_id' => function (array $attributes) use ($suffix): int {
                $receiptId = $attributes['receipt_id'] ?? null;
                $receipt = $receiptId ? PurchaseReceipt::query()->find($receiptId) : null;

                if ($receipt) {
                    return $receipt->supplier_id;
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
            'order_id' => function (array $attributes): ?int {
                $receiptId = $attributes['receipt_id'] ?? null;
                $receipt = $receiptId ? PurchaseReceipt::query()->find($receiptId) : null;

                return $receipt?->order_id;
            },
            'numero_documento' => 'AP-' . $this->faker->unique()->numberBetween(1, 999999),
            'data_emissao' => $this->faker->date(),
            'data_vencimento' => $this->faker->date(),
            'valor_total' => $this->faker->randomFloat(2, 10, 200),
            'status' => 'aberto',
        ];
    }
}
