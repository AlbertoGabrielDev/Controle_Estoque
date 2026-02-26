<?php

namespace Tests\Unit\Modules;

use App\Models\Fornecedor;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Models\PurchaseOrderItem;
use Modules\Purchases\Models\PurchasePayable;
use Modules\Purchases\Models\PurchaseReceiptItem;
use Modules\Purchases\Services\PurchaseReceiptService;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class PurchaseReceiptServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure partial receipts mark divergences during checking.
     *
     * @return void
     * @throws \Throwable
     */
    public function test_register_receipt_partial_and_check_flags_divergence(): void
    {
        [$order, $orderItem] = $this->createOrderWithItem(10, 5.0);

        $service = app(PurchaseReceiptService::class);

        $receipt = $service->registerReceipt([
            'order_id' => $order->id,
            'data_recebimento' => now()->toDateString(),
            'observacoes' => null,
            'items' => [
                [
                    'order_item_id' => $orderItem->id,
                    'quantidade_recebida' => 4,
                    'preco_unit_recebido' => 5.0,
                ],
            ],
        ]);

        $order->refresh();
        $this->assertSame('parcialmente_recebido', $order->status);

        $checked = $service->checkReceipt($receipt->id);

        $this->assertSame('com_divergencia', $checked->status);

        $receiptItem = PurchaseReceiptItem::query()->where('receipt_id', $receipt->id)->first();
        $this->assertNotNull($receiptItem);
        $this->assertTrue((bool) $receiptItem->divergencia_flag);

        $this->assertSame(0, PurchasePayable::query()->count());
    }

    /**
     * Ensure receipts without divergences generate payables.
     *
     * @return void
     * @throws \Throwable
     */
    public function test_check_receipt_without_divergence_creates_payable(): void
    {
        [$order, $orderItem] = $this->createOrderWithItem(5, 12.5);

        $service = app(PurchaseReceiptService::class);

        $receipt = $service->registerReceipt([
            'order_id' => $order->id,
            'data_recebimento' => now()->toDateString(),
            'observacoes' => null,
            'items' => [
                [
                    'order_item_id' => $orderItem->id,
                    'quantidade_recebida' => 5,
                    'preco_unit_recebido' => 12.5,
                ],
            ],
        ]);

        $checked = $service->checkReceipt($receipt->id);

        $this->assertSame('conferido', $checked->status);
        $this->assertDatabaseHas('purchase_payables', [
            'receipt_id' => $receipt->id,
            'status' => 'aberto',
        ]);

        $order->refresh();
        $this->assertSame('recebido', $order->status);
    }

    /**
     * Create an order with a single item.
     *
     * @param float $quantity
     * @param float $price
     * @return array{0: \Modules\Purchases\Models\PurchaseOrder, 1: \Modules\Purchases\Models\PurchaseOrderItem}
     */
    private function createOrderWithItem(float $quantity, float $price): array
    {
        $supplier = $this->createSupplier('REC');
        $item = $this->createItem('REC');

        $order = PurchaseOrder::query()->create([
            'numero' => 'PO-000001',
            'status' => 'emitido',
            'supplier_id' => $supplier->id_fornecedor,
            'quotation_id' => null,
            'data_emissao' => now()->toDateString(),
            'data_prevista' => null,
            'observacoes' => null,
            'total' => $quantity * $price,
        ]);

        $orderItem = PurchaseOrderItem::query()->create([
            'order_id' => $order->id,
            'item_id' => $item->id,
            'descricao_snapshot' => $item->nome,
            'unidade_medida_id' => null,
            'quantidade_pedida' => $quantity,
            'quantidade_recebida' => 0,
            'preco_unit' => $price,
            'imposto_id' => null,
            'aliquota_snapshot' => null,
            'total_linha' => $quantity * $price,
        ]);

        return [$order, $orderItem];
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
     * Create a basic item for receipts.
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
