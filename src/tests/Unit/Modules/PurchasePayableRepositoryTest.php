<?php

namespace Tests\Unit\Modules;

use App\Models\Fornecedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Models\PurchaseOrderItem;
use Modules\Purchases\Models\PurchasePayable;
use Modules\Purchases\Models\PurchaseReceipt;
use Modules\Purchases\Repositories\PurchasePayableRepository;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class PurchasePayableRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the repository can find a payable with relations.
     *
     * @return void
     */
    public function test_find_by_id_with_relations(): void
    {
        $supplier = $this->createSupplier('PAY');

        $order = PurchaseOrder::query()->create([
            'numero' => 'PO-PAY001',
            'status' => 'recebido',
            'supplier_id' => $supplier->id_fornecedor,
            'data_emissao' => now()->toDateString(),
            'total' => 100.00,
        ]);

        $receipt = PurchaseReceipt::query()->create([
            'numero' => 'REC-PAY001',
            'status' => 'conferido',
            'order_id' => $order->id,
            'supplier_id' => $supplier->id_fornecedor,
            'data_recebimento' => now()->toDateString(),
        ]);

        $payable = PurchasePayable::query()->create([
            'numero_documento' => 'PAG-001',
            'status' => 'aberto',
            'receipt_id' => $receipt->id,
            'order_id' => $order->id,
            'supplier_id' => $supplier->id_fornecedor,
            'valor_total' => 100.00,
            'data_emissao' => now()->toDateString(),
            'data_vencimento' => now()->addDays(30)->toDateString(),
        ]);

        /** @var PurchasePayableRepository $repository */
        $repository = app(PurchasePayableRepository::class);

        $found = $repository->findByIdWithRelations($payable->id, ['supplier', 'order', 'receipt']);

        $this->assertSame($payable->id, $found->id);
        $this->assertTrue($found->relationLoaded('supplier'));
        $this->assertTrue($found->relationLoaded('order'));
        $this->assertTrue($found->relationLoaded('receipt'));
        $this->assertSame($supplier->id_fornecedor, $found->supplier->id_fornecedor);
    }

    /**
     * Create a supplier.
     *
     * @param string $suffix
     * @return Fornecedor
     */
    private function createSupplier(string $suffix): Fornecedor
    {
        static $sequence = 50;
        $cnpjSuffix = str_pad((string) $sequence, 2, '0', STR_PAD_LEFT);
        $sequence = $sequence >= 99 ? 50 : $sequence + 1;

        $user = User::factory()->create();

        return Fornecedor::query()->create([
            'codigo' => 'FOR' . Str::upper($suffix),
            'razao_social' => 'Fornecedor ' . $suffix,
            'nome_fornecedor' => 'Fornecedor ' . $suffix,
            'cnpj' => '12.345.678/0001-' . $cnpjSuffix,
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
        ]);
    }
}
