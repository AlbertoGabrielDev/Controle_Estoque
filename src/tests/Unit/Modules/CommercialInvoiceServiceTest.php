<?php

namespace Tests\Unit\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Commercial\Models\CommercialDocumentSequence;
use Modules\Commercial\Models\CommercialSalesOrderItem;
use Modules\Commercial\Services\InvoiceService;
use Modules\Commercial\Services\SalesOrderService;
use Modules\Customers\Models\Cliente;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class CommercialInvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    private SalesOrderService $orderService;
    private InvoiceService $invoiceService;
    private int $clienteId;
    private int $itemId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderService = app(SalesOrderService::class);
        $this->invoiceService = app(InvoiceService::class);

        foreach (['SO', 'INV', 'AR'] as $type) {
            CommercialDocumentSequence::query()->create([
                'type' => $type,
                'last_number' => 0,
            ]);
        }

        $suffix = Str::random(6);
        $cliente = Cliente::query()->create([
            'nome' => 'Cliente ' . $suffix,
            'cpf_cnpj' => '000.000.000-' . random_int(10, 99),
            'email' => 'invoice_' . $suffix . '@example.com',
            'status' => 1,
            'ativo' => true,
        ]);
        $this->clienteId = $cliente->id_cliente;

        $this->itemId = (int) DB::table('itens')->insertGetId([
            'sku' => 'SKU-INV-' . Str::upper(Str::random(4)),
            'nome' => 'Item Invoice',
            'ativo' => true,
        ]);
    }

    /**
     * Create and confirm a sales order with one item.
     *
     * @return array{order_id:int,order_item_id:int}
     */
    private function createConfirmedOrder(): array
    {
        $order = $this->orderService->createOrder([
            'cliente_id' => $this->clienteId,
            'items' => [[
                'item_id' => $this->itemId,
                'descricao_snapshot' => 'Item Invoice',
                'quantidade' => 2,
                'preco_unit' => 120,
                'desconto_valor' => 0,
                'aliquota_snapshot' => 0,
                'total_linha' => 240,
            ]],
        ]);

        $this->orderService->confirmOrder($order->id);

        $orderItem = CommercialSalesOrderItem::query()
            ->where('order_id', $order->id)
            ->firstOrFail();

        return [
            'order_id' => (int) $order->id,
            'order_item_id' => (int) $orderItem->id,
        ];
    }

    public function test_create_partial_invoice_updates_order_status_and_creates_receivable(): void
    {
        $orderData = $this->createConfirmedOrder();

        $invoice = $this->invoiceService->createPartialInvoice($orderData['order_id'], [[
            'order_item_id' => $orderData['order_item_id'],
            'item_id' => $this->itemId,
            'descricao_snapshot' => 'Item Invoice',
            'quantidade_faturada' => 1,
            'preco_unit' => 120,
            'desconto_valor' => 0,
            'aliquota_snapshot' => 0,
        ]]);

        $this->assertSame('INV-000001', $invoice->numero);
        $this->assertDatabaseHas('commercial_sales_orders', [
            'id' => $orderData['order_id'],
            'status' => 'faturado_parcial',
        ]);
        $this->assertDatabaseHas('commercial_sales_receivables', [
            'invoice_id' => $invoice->id,
            'status' => 'aberto',
        ]);
    }

    public function test_cancel_invoice_reverses_receivable_and_item_billed_quantity(): void
    {
        $orderData = $this->createConfirmedOrder();

        $invoice = $this->invoiceService->createPartialInvoice($orderData['order_id'], [[
            'order_item_id' => $orderData['order_item_id'],
            'item_id' => $this->itemId,
            'descricao_snapshot' => 'Item Invoice',
            'quantidade_faturada' => 2,
            'preco_unit' => 120,
            'desconto_valor' => 0,
            'aliquota_snapshot' => 0,
        ]]);

        $cancelled = $this->invoiceService->cancelInvoice($invoice->id);

        $this->assertSame('cancelada', $cancelled->status);
        $this->assertDatabaseHas('commercial_sales_receivables', [
            'invoice_id' => $invoice->id,
            'status' => 'estornado',
        ]);
        $this->assertDatabaseHas('commercial_sales_order_items', [
            'id' => $orderData['order_item_id'],
            'quantidade_faturada' => 0,
        ]);
        $this->assertDatabaseHas('commercial_sales_orders', [
            'id' => $orderData['order_id'],
            'status' => 'confirmado',
        ]);
    }
}
