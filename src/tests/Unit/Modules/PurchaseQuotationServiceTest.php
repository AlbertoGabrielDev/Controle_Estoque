<?php

namespace Tests\Unit\Modules;

use App\Models\Fornecedor;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Purchases\Models\PurchaseQuotation;
use Modules\Purchases\Models\PurchaseRequisition;
use Modules\Purchases\Models\PurchaseRequisitionItem;
use Modules\Purchases\Services\PurchaseQuotationService;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class PurchaseQuotationServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the full quotation workflow: create -> add supplier -> register prices -> select winner -> close.
     *
     * @return void
     * @throws \Throwable
     */
    public function test_full_quotation_workflow(): void
    {
        [$requisition, $item, $supplier] = $this->createRequisitionWithItems();

        $service = app(PurchaseQuotationService::class);

        // 1. Create quotation from requisition
        $quotation = $service->createFromRequisition($requisition->id, [
            'data_limite' => now()->addDays(7)->toDateString(),
            'observacoes' => 'Cotacao de teste',
            'supplier_ids' => [$supplier->id_fornecedor],
        ]);

        $this->assertNotNull($quotation);
        $this->assertSame('aberta', $quotation->status);
        $this->assertSame($requisition->id, $quotation->requisition_id);
        $this->assertCount(1, $quotation->suppliers);

        // 2. Register supplier prices
        $quotationSupplier = $quotation->suppliers->first();
        $this->assertCount(1, $quotationSupplier->items);

        $supplierItem = $quotationSupplier->items->first();

        $result = $service->registerSupplierPrices($quotationSupplier->id, [
            [
                'requisition_item_id' => $supplierItem->requisition_item_id,
                'preco_unit' => 15.50,
            ],
        ]);

        $this->assertSame('respondeu', $result->status);
        $supplierItem->refresh();
        $this->assertSame(15.50, (float) $supplierItem->preco_unit);

        // 3. Select winner
        $winner = $service->selectWinnerForItem($quotation->id, $supplierItem->id);
        $this->assertTrue((bool) $winner->selecionado);

        // 4. Close quotation
        $closed = $service->closeQuotation($quotation->id);
        $this->assertSame('encerrada', $closed->status);
    }

    /**
     * Test cancel quotation.
     *
     * @return void
     * @throws \Throwable
     */
    public function test_cancel_quotation(): void
    {
        [$requisition] = $this->createRequisitionWithItems();

        $service = app(PurchaseQuotationService::class);

        $quotation = $service->createFromRequisition($requisition->id, [
            'data_limite' => now()->addDays(7)->toDateString(),
            'observacoes' => null,
        ]);

        $cancelled = $service->cancelQuotation($quotation->id);
        $this->assertSame('cancelada', $cancelled->status);
    }

    /**
     * Test that closing a quotation without selecting winners throws exception.
     *
     * @return void
     * @throws \Throwable
     */
    public function test_close_quotation_without_winners_throws(): void
    {
        [$requisition, , $supplier] = $this->createRequisitionWithItems();

        $service = app(PurchaseQuotationService::class);

        $quotation = $service->createFromRequisition($requisition->id, [
            'data_limite' => now()->addDays(7)->toDateString(),
            'observacoes' => null,
            'supplier_ids' => [$supplier->id_fornecedor],
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cada item deve ter exatamente um fornecedor vencedor.');

        $service->closeQuotation($quotation->id);
    }

    /**
     * Create a requisition with items for tests.
     *
     * @return array
     */
    private function createRequisitionWithItems(): array
    {
        $supplier = $this->createSupplier('QOT');
        $item = $this->createItem('QOT');

        $requisition = PurchaseRequisition::query()->create([
            'numero' => 'REQ-T001',
            'status' => 'aprovado',
            'data_requisicao' => now()->toDateString(),
            'observacoes' => null,
        ]);

        PurchaseRequisitionItem::query()->create([
            'requisition_id' => $requisition->id,
            'item_id' => $item->id,
            'descricao_snapshot' => $item->nome,
            'unidade_medida_id' => null,
            'quantidade' => 10,
            'preco_estimado' => 12.00,
            'imposto_id' => null,
            'total_linha' => 120.00,
        ]);

        return [$requisition, $item, $supplier];
    }

    /**
     * Create a supplier.
     *
     * @param string $suffix
     * @return Fornecedor
     */
    private function createSupplier(string $suffix): Fornecedor
    {
        static $sequence = 30;
        $cnpjSuffix = str_pad((string) $sequence, 2, '0', STR_PAD_LEFT);
        $sequence = $sequence >= 99 ? 30 : $sequence + 1;

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

    /**
     * Create an item.
     *
     * @param string $suffix
     * @return Item
     */
    private function createItem(string $suffix): Item
    {
        return Item::query()->create([
            'sku' => 'SKU-' . Str::upper(Str::random(6)) . '-' . $suffix,
            'nome' => 'Item ' . $suffix,
            'tipo' => 'produto',
            'preco_base' => 12.0,
            'custo' => 6.0,
            'ativo' => true,
        ]);
    }
}
