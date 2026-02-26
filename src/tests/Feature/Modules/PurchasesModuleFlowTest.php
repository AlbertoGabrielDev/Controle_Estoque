<?php

namespace Tests\Feature\Modules;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Fornecedor;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Models\PurchaseQuotation;
use Modules\Purchases\Models\PurchaseQuotationSupplier;
use Modules\Purchases\Models\PurchaseQuotationSupplierItem;
use Modules\Purchases\Models\PurchaseReceipt;
use Modules\Purchases\Models\PurchaseRequisition;
use Modules\Purchases\Models\PurchaseReturn;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class PurchasesModuleFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prepare the test environment for web routes.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'http://localhost']);
        URL::forceRootUrl('http://localhost');
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    /**
     * Validate core purchase flow endpoints end-to-end.
     *
     * @return void
     */
    public function test_purchase_flow_endpoints_create_records_and_update_statuses(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $supplier = $this->createSupplier('FLOW');
        $item = $this->createItem('FLOW');

        $requisitionPayload = [
            'observacoes' => 'Requisicao teste',
            'data_requisicao' => now()->toDateString(),
            'items' => [
                [
                    'item_id' => $item->id,
                    'descricao_snapshot' => $item->nome,
                    'unidade_medida_id' => null,
                    'quantidade' => 5,
                    'preco_estimado' => 12.5,
                    'imposto_id' => null,
                    'observacoes' => null,
                ],
            ],
        ];

        $this->post(route('purchases.requisitions.store'), $requisitionPayload)
            ->assertRedirect();

        $requisition = PurchaseRequisition::query()->latest('id')->firstOrFail();
        $this->assertSame('draft', $requisition->status);

        $this->patch(route('purchases.requisitions.approve', $requisition->id))
            ->assertRedirect();

        $requisition->refresh();
        $this->assertSame('aprovado', $requisition->status);

        $this->post(route('purchases.quotations.store'), [
            'requisition_id' => $requisition->id,
            'data_limite' => null,
            'observacoes' => null,
            'supplier_ids' => [$supplier->id_fornecedor],
        ])->assertRedirect();

        $quotation = PurchaseQuotation::query()->latest('id')->firstOrFail();
        $this->assertSame('aberta', $quotation->status);

        $quotationSupplier = PurchaseQuotationSupplier::query()
            ->where('quotation_id', $quotation->id)
            ->first();
        $this->assertNotNull($quotationSupplier);

        $requisitionItem = $requisition->items()->first();
        $this->assertNotNull($requisitionItem);

        $this->patch(route('purchases.quotations.registerPrices', [
            'quotationId' => $quotation->id,
            'quotationSupplierId' => $quotationSupplier->id,
        ]), [
            'items' => [
                [
                    'requisition_item_id' => $requisitionItem->id,
                    'quantidade' => 5,
                    'preco_unit' => 12.5,
                ],
            ],
        ])->assertRedirect();

        $supplierItem = PurchaseQuotationSupplierItem::query()
            ->where('quotation_supplier_id', $quotationSupplier->id)
            ->first();
        $this->assertNotNull($supplierItem);

        $this->patch(route('purchases.quotations.selectItem', $quotation->id), [
            'quotation_supplier_item_id' => $supplierItem->id,
        ])->assertRedirect();

        $supplierItem->refresh();
        $this->assertTrue((bool) $supplierItem->selecionado);

        $this->patch(route('purchases.quotations.close', $quotation->id))
            ->assertRedirect();

        $quotation->refresh();
        $this->assertSame('encerrada', $quotation->status);

        $this->post(route('purchases.orders.fromQuotation'), [
            'quotation_id' => $quotation->id,
        ])->assertRedirect();

        $order = PurchaseOrder::query()->latest('id')->firstOrFail();
        $this->assertSame($supplier->id_fornecedor, $order->supplier_id);

        $orderItem = $order->items()->first();
        $this->assertNotNull($orderItem);

        $this->post(route('purchases.receipts.store'), [
            'order_id' => $order->id,
            'data_recebimento' => now()->toDateString(),
            'observacoes' => null,
            'items' => [
                [
                    'order_item_id' => $orderItem->id,
                    'quantidade_recebida' => 2,
                    'preco_unit_recebido' => 12.5,
                ],
            ],
        ])->assertRedirect();

        $receipt = PurchaseReceipt::query()->latest('id')->firstOrFail();
        $this->assertSame('registrado', $receipt->status);

        $order->refresh();
        $this->assertSame('parcialmente_recebido', $order->status);

        $this->patch(route('purchases.receipts.check', $receipt->id))
            ->assertRedirect();

        $receipt->refresh();
        $this->assertSame('com_divergencia', $receipt->status);

        $this->patch(route('purchases.receipts.acceptDivergence', $receipt->id))
            ->assertRedirect();

        $receipt->refresh();
        $this->assertSame('conferido', $receipt->status);
        $this->assertDatabaseHas('purchase_payables', [
            'receipt_id' => $receipt->id,
        ]);

        $receiptItem = $receipt->items()->first();
        $this->assertNotNull($receiptItem);

        $this->post(route('purchases.returns.store'), [
            'receipt_id' => $receipt->id,
            'motivo' => 'Devolucao teste',
            'data_devolucao' => now()->toDateString(),
            'items' => [
                [
                    'receipt_item_id' => $receiptItem->id,
                    'item_id' => $receiptItem->item_id,
                    'quantidade_devolvida' => 1,
                    'observacoes' => null,
                ],
            ],
        ])->assertRedirect();

        $purchaseReturn = PurchaseReturn::query()->latest('id')->firstOrFail();

        $this->patch(route('purchases.returns.confirm', $purchaseReturn->id))
            ->assertRedirect();

        $purchaseReturn->refresh();
        $this->assertSame('confirmada', $purchaseReturn->status);

        $orderItem->refresh();
        $this->assertSame(1.0, (float) $orderItem->quantidade_recebida);
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
     * Create a basic item for purchase flow.
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
