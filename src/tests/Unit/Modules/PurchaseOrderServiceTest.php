<?php

namespace Tests\Unit\Modules;

use App\Models\Fornecedor;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Purchases\Models\PurchaseQuotation;
use Modules\Purchases\Models\PurchaseQuotationSupplier;
use Modules\Purchases\Models\PurchaseQuotationSupplierItem;
use Modules\Purchases\Models\PurchaseRequisition;
use Modules\Purchases\Models\PurchaseRequisitionItem;
use Modules\Purchases\Services\PurchaseOrderService;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class PurchaseOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure orders are generated per winning supplier.
     *
     * @return void
     * @throws \Throwable
     */
    public function test_create_from_quotation_generates_orders_per_supplier(): void
    {
        $itemA = $this->createItem('A');
        $itemB = $this->createItem('B');

        $requisition = PurchaseRequisition::query()->create([
            'numero' => 'REQ-000001',
            'status' => 'aprovado',
            'solicitado_por' => null,
            'observacoes' => null,
            'data_requisicao' => now()->toDateString(),
        ]);

        $reqItemA = PurchaseRequisitionItem::query()->create([
            'requisition_id' => $requisition->id,
            'item_id' => $itemA->id,
            'descricao_snapshot' => $itemA->nome,
            'unidade_medida_id' => null,
            'quantidade' => 5,
            'preco_estimado' => 10,
            'imposto_id' => null,
            'observacoes' => null,
        ]);

        $reqItemB = PurchaseRequisitionItem::query()->create([
            'requisition_id' => $requisition->id,
            'item_id' => $itemB->id,
            'descricao_snapshot' => $itemB->nome,
            'unidade_medida_id' => null,
            'quantidade' => 3,
            'preco_estimado' => 20,
            'imposto_id' => null,
            'observacoes' => null,
        ]);

        $quotation = PurchaseQuotation::query()->create([
            'numero' => 'COT-000001',
            'status' => 'encerrada',
            'requisition_id' => $requisition->id,
            'data_limite' => null,
            'observacoes' => null,
        ]);

        $supplierA = $this->createSupplier('A');
        $supplierB = $this->createSupplier('B');

        $quotationSupplierA = PurchaseQuotationSupplier::query()->create([
            'quotation_id' => $quotation->id,
            'supplier_id' => $supplierA->id_fornecedor,
            'status' => 'respondeu',
            'prazo_entrega_dias' => 5,
            'condicao_pagamento' => null,
            'observacoes' => null,
        ]);

        $quotationSupplierB = PurchaseQuotationSupplier::query()->create([
            'quotation_id' => $quotation->id,
            'supplier_id' => $supplierB->id_fornecedor,
            'status' => 'respondeu',
            'prazo_entrega_dias' => 7,
            'condicao_pagamento' => null,
            'observacoes' => null,
        ]);

        PurchaseQuotationSupplierItem::query()->create([
            'quotation_supplier_id' => $quotationSupplierA->id,
            'requisition_item_id' => $reqItemA->id,
            'item_id' => $itemA->id,
            'quantidade' => 5,
            'preco_unit' => 10,
            'imposto_id' => null,
            'aliquota_snapshot' => null,
            'selecionado' => true,
        ]);

        PurchaseQuotationSupplierItem::query()->create([
            'quotation_supplier_id' => $quotationSupplierB->id,
            'requisition_item_id' => $reqItemB->id,
            'item_id' => $itemB->id,
            'quantidade' => 3,
            'preco_unit' => 20,
            'imposto_id' => null,
            'aliquota_snapshot' => null,
            'selecionado' => true,
        ]);

        $service = app(PurchaseOrderService::class);
        $orders = $service->createFromQuotation($quotation->id);

        $this->assertCount(2, $orders);

        $ordersBySupplier = collect($orders)->keyBy('supplier_id');

        $this->assertSame(50.0, (float) $ordersBySupplier[$supplierA->id_fornecedor]->total);
        $this->assertSame(60.0, (float) $ordersBySupplier[$supplierB->id_fornecedor]->total);

        $this->assertDatabaseHas('purchase_orders', [
            'supplier_id' => $supplierA->id_fornecedor,
            'quotation_id' => $quotation->id,
        ]);

        $this->assertDatabaseHas('purchase_orders', [
            'supplier_id' => $supplierB->id_fornecedor,
            'quotation_id' => $quotation->id,
        ]);
    }

    /**
     * Create a supplier with required fields.
     *
     * @param string $suffix
     * @return \App\Models\Fornecedor
     */
    private function createSupplier(string $suffix): Fornecedor
    {
        static $sequence = 10;
        $cnpjSuffix = str_pad((string) $sequence, 2, '0', STR_PAD_LEFT);
        $sequence = $sequence >= 99 ? 10 : $sequence + 1;

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
     * Create a basic item for requisitions.
     *
     * @param string $suffix
     * @return \App\Models\Item
     */
    private function createItem(string $suffix): Item
    {
        return Item::query()->create([
            'sku' => 'SKU-' . Str::upper(Str::random(6)) . '-' . $suffix,
            'nome' => 'Item ' . $suffix,
            'tipo' => 'produto',
            'preco_base' => 10.5,
            'custo' => 5.5,
            'ativo' => true,
        ]);
    }
}
