<?php

namespace Tests\Unit\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Commercial\Models\CommercialDocumentSequence;
use Modules\Commercial\Services\InvoiceService;
use Modules\Commercial\Services\SalesOrderService;
use Modules\Commercial\Services\SalesReturnService;
use Modules\Customers\Models\Cliente;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class CommercialSalesReturnServiceTest extends TestCase
{
    use RefreshDatabase;

    private SalesOrderService $orderService;
    private InvoiceService $invoiceService;
    private SalesReturnService $returnService;
    private int $clienteId;
    private int $itemId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderService = app(SalesOrderService::class);
        $this->invoiceService = app(InvoiceService::class);
        $this->returnService = app(SalesReturnService::class);

        foreach (['SO', 'INV', 'RET', 'AR'] as $type) {
            CommercialDocumentSequence::query()->create([
                'type' => $type,
                'last_number' => 0,
            ]);
        }

        $suffix = Str::random(6);
        $cliente = Cliente::query()->create([
            'nome' => 'Cliente ' . $suffix,
            'cpf_cnpj' => '000.000.000-' . random_int(10, 99),
            'email' => 'return_' . $suffix . '@example.com',
            'status' => 1,
            'ativo' => true,
        ]);
        $this->clienteId = $cliente->id_cliente;

        $this->itemId = (int) DB::table('itens')->insertGetId([
            'sku' => 'SKU-RET-' . Str::upper(Str::random(4)),
            'nome' => 'Item Return',
            'ativo' => true,
        ]);
    }

    public function test_confirm_return_marks_status_and_reverses_receivable(): void
    {
        $order = $this->orderService->createOrder([
            'cliente_id' => $this->clienteId,
            'items' => [[
                'item_id' => $this->itemId,
                'descricao_snapshot' => 'Item Return',
                'quantidade' => 1,
                'preco_unit' => 90,
                'desconto_valor' => 0,
                'aliquota_snapshot' => 0,
                'total_linha' => 90,
            ]],
        ]);
        $this->orderService->confirmOrder($order->id);

        $orderItem = $order->items()->firstOrFail();

        $invoice = $this->invoiceService->createPartialInvoice($order->id, [[
            'order_item_id' => $orderItem->id,
            'item_id' => $this->itemId,
            'descricao_snapshot' => 'Item Return',
            'quantidade_faturada' => 1,
            'preco_unit' => 90,
            'desconto_valor' => 0,
            'aliquota_snapshot' => 0,
        ]]);

        $invoiceItem = $invoice->items()->firstOrFail();

        $salesReturn = $this->returnService->createReturn([
            'invoice_id' => $invoice->id,
            'order_id' => $order->id,
            'cliente_id' => $this->clienteId,
            'motivo' => 'Devolucao de teste',
            'items' => [[
                'invoice_item_id' => $invoiceItem->id,
                'order_item_id' => $orderItem->id,
                'item_id' => $this->itemId,
                'quantidade_devolvida' => 1,
            ]],
        ]);

        $confirmed = $this->returnService->confirmReturn($salesReturn->id);

        $this->assertSame('confirmada', $confirmed->status);
        $this->assertDatabaseHas('commercial_sales_receivables', [
            'invoice_id' => $invoice->id,
            'status' => 'estornado',
        ]);
        $this->assertDatabaseHas('commercial_sales_order_items', [
            'id' => $orderItem->id,
            'quantidade_faturada' => 0,
        ]);
    }

    public function test_cancel_return_changes_status_to_cancelada(): void
    {
        $salesReturn = $this->returnService->createReturn([
            'cliente_id' => $this->clienteId,
            'motivo' => 'Cancelamento de teste',
            'items' => [[
                'item_id' => $this->itemId,
                'quantidade_devolvida' => 1,
            ]],
        ]);

        $cancelled = $this->returnService->cancelReturn($salesReturn->id);

        $this->assertSame('cancelada', $cancelled->status);
    }
}
