<?php

namespace Tests\Unit\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Commercial\Models\CommercialDocumentSequence;
use Modules\Commercial\Services\ProposalService;
use Modules\Commercial\Services\SalesOrderService;
use Modules\Customers\Models\Cliente;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class CommercialProposalServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProposalService $proposalService;
    private SalesOrderService $orderService;
    private int $clienteId;
    private int $itemId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->proposalService = app(ProposalService::class);
        $this->orderService = app(SalesOrderService::class);

        foreach (['PROP', 'SO'] as $type) {
            CommercialDocumentSequence::query()->create([
                'type' => $type,
                'last_number' => 0,
            ]);
        }

        $suffix = Str::random(6);
        $cliente = Cliente::query()->create([
            'nome' => 'Cliente ' . $suffix,
            'cpf_cnpj' => '000.000.000-' . random_int(10, 99),
            'email' => 'proposal_' . $suffix . '@example.com',
            'status' => 1,
            'ativo' => true,
        ]);
        $this->clienteId = $cliente->id_cliente;

        $this->itemId = (int) DB::table('itens')->insertGetId([
            'sku' => 'SKU-PROP-' . Str::upper(Str::random(4)),
            'nome' => 'Item Proposal',
            'ativo' => true,
        ]);
    }

    public function test_create_proposal_generates_number_and_draft_status(): void
    {
        $proposal = $this->proposalService->createProposal([
            'cliente_id' => $this->clienteId,
            'items' => [[
                'item_id' => $this->itemId,
                'descricao_snapshot' => 'Item Proposal',
                'quantidade' => 2,
                'preco_unit' => 100,
                'desconto_valor' => 10,
                'aliquota_snapshot' => 5,
                'total_linha' => 199.5,
            ]],
        ]);

        $this->assertSame('PROP-000001', $proposal->numero);
        $this->assertSame('rascunho', $proposal->status);
        $this->assertCount(1, $proposal->items);
    }

    public function test_send_and_approve_proposal_flow(): void
    {
        $proposal = $this->proposalService->createProposal([
            'cliente_id' => $this->clienteId,
            'items' => [[
                'item_id' => $this->itemId,
                'descricao_snapshot' => 'Item Proposal',
                'quantidade' => 1,
                'preco_unit' => 50,
                'desconto_valor' => 0,
                'aliquota_snapshot' => 0,
                'total_linha' => 50,
            ]],
        ]);

        $sent = $this->proposalService->sendProposal($proposal->id);
        $this->assertSame('enviada', $sent->status);

        $approved = $this->proposalService->approveProposal($proposal->id);
        $this->assertSame('aprovada', $approved->status);
    }

    public function test_convert_approved_proposal_to_order_marks_proposal_as_converted(): void
    {
        $proposal = $this->proposalService->createProposal([
            'cliente_id' => $this->clienteId,
            'items' => [[
                'item_id' => $this->itemId,
                'descricao_snapshot' => 'Item Proposal',
                'quantidade' => 1,
                'preco_unit' => 75,
                'desconto_valor' => 0,
                'aliquota_snapshot' => 0,
                'total_linha' => 75,
            ]],
        ]);

        $this->proposalService->sendProposal($proposal->id);
        $this->proposalService->approveProposal($proposal->id);

        $order = $this->proposalService->convertToSalesOrder($proposal->id, $this->orderService);

        $proposal->refresh();

        $this->assertSame('convertida', $proposal->status);
        $this->assertSame($proposal->id, $order->proposal_id);
        $this->assertSame('SO-000001', $order->numero);
    }
}
