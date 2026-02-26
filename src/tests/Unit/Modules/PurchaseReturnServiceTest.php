<?php

namespace Tests\Unit\Modules;

use App\Models\Fornecedor;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Models\PurchaseOrderItem;
use Modules\Purchases\Models\PurchaseReceipt;
use Modules\Purchases\Models\PurchaseReceiptItem;
use Modules\Purchases\Services\PurchaseReturnService;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class PurchaseReturnServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure confirming a return reduces quantities and updates order status.
     *
     * @return void
     * @throws \Throwable
     */
    public function test_confirm_return_reduces_quantities_and_updates_status(): void
    {
        [$order, $orderItem, $receipt, $receiptItem] = $this->createOrderWithReceipt(10, 5.0);

        $service = app(PurchaseReturnService::class);

        $return = $service->createReturn([
            'receipt_id' => $receipt->id,
            'order_id' => $order->id,
            'motivo' => 'Devolucao de teste',
            'data_devolucao' => now()->toDateString(),
            'items' => [
                [
                    'receipt_item_id' => $receiptItem->id,
                    'order_item_id' => $orderItem->id,
                    'item_id' => $orderItem->item_id,
                    'quantidade_devolvida' => 4,
                    'observacoes' => null,
                ],
            ],
        ]);

        $confirmed = $service->confirmReturn($return->id);

        $orderItem->refresh();
        $receiptItem->refresh();
        $order->refresh();

        $this->assertSame(6.0, (float) $orderItem->quantidade_recebida);
        $this->assertSame(6.0, (float) $receiptItem->quantidade_recebida);
        $this->assertSame('parcialmente_recebido', $order->status);
        $this->assertSame('confirmada', $confirmed->status);
    }

    /**
     * Create an order with a receipt already registered.
     *
     * @param float $quantity
     * @param float $price
     * @return array{0: \Modules\Purchases\Models\PurchaseOrder, 1: \Modules\Purchases\Models\PurchaseOrderItem, 2: \Modules\Purchases\Models\PurchaseReceipt, 3: \Modules\Purchases\Models\PurchaseReceiptItem}
     */
    private function createOrderWithReceipt(float $quantity, float $price): array
    {
        $supplier = $this->createSupplier('RET');
        $item = $this->createItem('RET');

        $order = PurchaseOrder::query()->create([
            'numero' => 'PO-000010',
            'status' => 'recebido',
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
            'quantidade_recebida' => $quantity,
            'preco_unit' => $price,
            'imposto_id' => null,
            'aliquota_snapshot' => null,
            'total_linha' => $quantity * $price,
        ]);

        $receipt = PurchaseReceipt::query()->create([
            'numero' => 'REC-000010',
            'status' => 'conferido',
            'order_id' => $order->id,
            'supplier_id' => $supplier->id_fornecedor,
            'data_recebimento' => now()->toDateString(),
            'observacoes' => null,
        ]);

        $receiptItem = PurchaseReceiptItem::query()->create([
            'receipt_id' => $receipt->id,
            'order_item_id' => $orderItem->id,
            'item_id' => $item->id,
            'quantidade_recebida' => $quantity,
            'preco_unit_recebido' => $price,
            'imposto_id' => null,
            'aliquota_snapshot' => null,
            'divergencia_flag' => false,
            'motivo_divergencia' => null,
        ]);

        return [$order, $orderItem, $receipt, $receiptItem];
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
     * Create a basic item for returns.
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
