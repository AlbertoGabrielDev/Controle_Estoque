<?php

namespace Tests\Feature\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Commercial\Models\CommercialDocumentSequence;
use Modules\Commercial\Models\CommercialOpportunity;
use Modules\Commercial\Models\CommercialProposal;
use Modules\Commercial\Models\CommercialSalesOrder;
use Modules\Commercial\Models\CommercialSalesInvoice;
use Modules\Commercial\Models\CommercialSalesReceivable;
use Modules\Commercial\Models\CommercialSalesReturn;
use Modules\Commercial\Services\CommercialDocumentNumberService;
use Modules\Commercial\Services\InvoiceService;
use Modules\Commercial\Services\OpportunityService;
use Modules\Commercial\Services\ProposalService;
use Modules\Commercial\Services\SalesOrderService;
use Modules\Commercial\Services\SalesReturnService;
use Modules\Customers\Models\Cliente;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class CommercialModuleFlowTest extends TestCase
{
    use RefreshDatabase;

    private OpportunityService $opportunityService;
    private ProposalService $proposalService;
    private SalesOrderService $orderService;
    private InvoiceService $invoiceService;
    private SalesReturnService $returnService;
    private int $clienteId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->opportunityService = app(OpportunityService::class);
        $this->proposalService    = app(ProposalService::class);
        $this->orderService       = app(SalesOrderService::class);
        $this->invoiceService     = app(InvoiceService::class);
        $this->returnService      = app(SalesReturnService::class);

        foreach (['OPP', 'PROP', 'SO', 'INV', 'RET', 'AR'] as $type) {
            CommercialDocumentSequence::query()->create(['type' => $type, 'last_number' => 0]);
        }

        $suffix = Str::random(6);
        $cliente = Cliente::query()->create([
            'nome'     => 'Cliente ' . $suffix,
            'cpf_cnpj' => '000.000.000-' . random_int(10, 99),
            'email'    => 'cli_' . $suffix . '@example.com',
            'status'   => 1,
            'ativo'    => true,
        ]);
        $this->clienteId = $cliente->id_cliente;
    }

    /** @return array{item_id: int} */
    private function seedItem(): array
    {
        $id = \Illuminate\Support\Facades\DB::table('itens')->insertGetId([
            'sku'   => 'SKU-' . Str::random(4),
            'nome'  => 'Produto Teste',
            'ativo' => true,
        ]);
        return ['item_id' => $id];
    }

    public function test_opportunity_to_proposal_flow(): void
    {
        // 1. Create opportunity
        $opportunity = $this->opportunityService->createOpportunity([
            'nome'           => 'Oportunidade Teste',
            'valor_estimado' => 1000,
        ]);
        $this->assertSame('novo', $opportunity->status);

        // 2. Advance to proposta_enviada
        $this->opportunityService->convertToProposal($opportunity->id);
        $opportunity->refresh();
        $this->assertSame('proposta_enviada', $opportunity->status);

        // 3. Create proposal from opportunity
        $proposal = $this->proposalService->createFromOpportunity($opportunity->id, [
            'cliente_id' => $this->clienteId,
            'items'      => [],
        ]);
        $this->assertInstanceOf(CommercialProposal::class, $proposal);
        $this->assertSame('rascunho', $proposal->status);
        $this->assertSame($opportunity->id, $proposal->opportunity_id);
    }

    public function test_proposal_approve_and_convert_to_order(): void
    {
        $proposal = CommercialProposal::query()->create([
            'numero'      => 'PROP-000001',
            'cliente_id'  => $this->clienteId,
            'status'      => 'enviada',
            'data_emissao'=> now()->format('Y-m-d'),
            'subtotal'    => 0, 'desconto_total' => 0, 'total_impostos' => 0, 'total' => 0,
        ]);

        // Approve
        $this->proposalService->approveProposal($proposal->id);
        $proposal->refresh();
        $this->assertSame('aprovada', $proposal->status);

        // Convert to order
        CommercialDocumentSequence::query()->updateOrCreate(['type' => 'SO'], ['last_number' => 0]);
        $order = $this->proposalService->convertToSalesOrder($proposal->id, $this->orderService);

        $this->assertInstanceOf(CommercialSalesOrder::class, $order);
        $this->assertSame($proposal->id, $order->proposal_id);

        $proposal->refresh();
        $this->assertSame('convertida', $proposal->status);
    }

    public function test_order_confirm_and_invoice_full(): void
    {
        $item = $this->seedItem();

        $order = $this->orderService->createOrder([
            'cliente_id' => $this->clienteId,
            'items'      => [[
                'item_id'            => $item['item_id'],
                'descricao_snapshot' => 'Produto A',
                'quantidade'         => 2,
                'preco_unit'         => 100,
                'desconto_valor'     => 0,
                'total_linha'        => 200,
            ]],
        ]);

        $this->orderService->confirmOrder($order->id);
        $order->refresh();
        $this->assertSame('confirmado', $order->status);

        $orderItem = $order->items()->first();

        $invoice = $this->invoiceService->createPartialInvoice($order->id, [[
            'order_item_id'       => $orderItem->id,
            'item_id'             => $item['item_id'],
            'descricao_snapshot'  => 'Produto A',
            'quantidade_faturada' => 2,
            'preco_unit'          => 100,
            'desconto_valor'      => 0,
            'aliquota_snapshot'   => 0,
        ]]);

        $this->assertInstanceOf(CommercialSalesInvoice::class, $invoice);
        $this->assertSame('emitida', $invoice->status);

        $order->refresh();
        $this->assertSame('faturado_total', $order->status);

        // AR generated
        $this->assertDatabaseHas('commercial_sales_receivables', [
            'invoice_id' => $invoice->id,
            'status'     => 'aberto',
        ]);
    }

    public function test_partial_invoice_then_remaining(): void
    {
        $item = $this->seedItem();

        $order = $this->orderService->createOrder([
            'cliente_id' => $this->clienteId,
            'items'      => [[
                'item_id'            => $item['item_id'],
                'descricao_snapshot' => 'Produto B',
                'quantidade'         => 4,
                'preco_unit'         => 50,
                'desconto_valor'     => 0,
                'total_linha'        => 200,
            ]],
        ]);
        $this->orderService->confirmOrder($order->id);

        $orderItem = $order->items()->first();

        // Bill 2 of 4
        $this->invoiceService->createPartialInvoice($order->id, [[
            'order_item_id' => $orderItem->id, 'item_id' => $item['item_id'],
            'descricao_snapshot' => 'Produto B', 'quantidade_faturada' => 2,
            'preco_unit' => 50, 'desconto_valor' => 0, 'aliquota_snapshot' => 0,
        ]]);
        $order->refresh();
        $this->assertSame('faturado_parcial', $order->status);

        // Bill remaining 2
        $this->invoiceService->createPartialInvoice($order->id, [[
            'order_item_id' => $orderItem->id, 'item_id' => $item['item_id'],
            'descricao_snapshot' => 'Produto B', 'quantidade_faturada' => 2,
            'preco_unit' => 50, 'desconto_valor' => 0, 'aliquota_snapshot' => 0,
        ]]);
        $order->refresh();
        $this->assertSame('faturado_total', $order->status);
    }

    public function test_return_reverses_receivable(): void
    {
        $item = $this->seedItem();

        $order = $this->orderService->createOrder([
            'cliente_id' => $this->clienteId,
            'items'      => [[
                'item_id' => $item['item_id'], 'descricao_snapshot' => 'Prod C',
                'quantidade' => 1, 'preco_unit' => 200, 'desconto_valor' => 0, 'total_linha' => 200,
            ]],
        ]);
        $this->orderService->confirmOrder($order->id);
        $orderItem = $order->items()->first();

        $invoice = $this->invoiceService->createPartialInvoice($order->id, [[
            'order_item_id' => $orderItem->id, 'item_id' => $item['item_id'],
            'descricao_snapshot' => 'Prod C', 'quantidade_faturada' => 1,
            'preco_unit' => 200, 'desconto_valor' => 0, 'aliquota_snapshot' => 0,
        ]]);

        $invoiceItem = $invoice->items()->first();

        $return = $this->returnService->createReturn([
            'invoice_id'    => $invoice->id,
            'cliente_id'    => $this->clienteId,
            'motivo'        => 'Produto com defeito',
            'data_devolucao'=> now()->format('Y-m-d'),
            'items'         => [[
                'invoice_item_id'     => $invoiceItem->id,
                'order_item_id'       => $orderItem->id,
                'item_id'             => $item['item_id'],
                'quantidade_devolvida'=> 1,
            ]],
        ]);
        $this->assertSame('aberta', $return->status);

        $this->returnService->confirmReturn($return->id);
        $return->refresh();
        $this->assertSame('confirmada', $return->status);

        // AR should be reversed
        $receivable = CommercialSalesReceivable::query()->where('invoice_id', $invoice->id)->first();
        $this->assertSame('estornado', $receivable->status);
    }
}
