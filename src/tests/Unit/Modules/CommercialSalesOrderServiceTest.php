<?php

namespace Tests\Unit\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Commercial\Models\CommercialDocumentSequence;
use Modules\Commercial\Services\SalesOrderService;
use Modules\Customers\Models\Cliente;
use PHPUnit\Framework\Attributes\Group;
use RuntimeException;
use Tests\TestCase;

#[Group('modularizacao')]
class CommercialSalesOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private SalesOrderService $service;
    private int $clienteId;
    private int $itemId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SalesOrderService::class);

        // Seed document sequence
        CommercialDocumentSequence::query()->create(['type' => 'SO', 'last_number' => 0]);

        // Create a minimal cliente
        $suffix = Str::random(6);
        $cliente = Cliente::query()->create([
            'nome'        => 'Cliente ' . $suffix,
            'cpf_cnpj'    => '000.000.000-' . random_int(10, 99),
            'email'       => 'cli_' . $suffix . '@example.com',
            'status'      => 1,
            'ativo'       => true,
        ]);
        $this->clienteId = $cliente->id_cliente;

        $this->itemId = (int) DB::table('itens')->insertGetId([
            'sku' => 'SKU-' . Str::upper(Str::random(4)),
            'nome' => 'Prod A',
            'ativo' => true,
        ]);
    }

    public function test_create_order_generates_numero_and_sets_draft(): void
    {
        $order = $this->service->createOrder([
            'cliente_id'  => $this->clienteId,
            'data_pedido' => now()->format('Y-m-d'),
            'items'       => [],
        ]);

        $this->assertSame('SO-000001', $order->numero);
        $this->assertSame('rascunho', $order->status);
    }

    public function test_confirm_order_changes_status_to_confirmado(): void
    {
        $order = $this->service->createOrder([
            'cliente_id'  => $this->clienteId,
            'items'       => [[
                'item_id' => $this->itemId, 'descricao_snapshot' => 'Prod A',
                'quantidade' => 1, 'preco_unit' => 100,
                'desconto_valor' => 0, 'total_linha' => 100,
            ]],
        ]);

        $confirmed = $this->service->confirmOrder($order->id);
        $this->assertSame('confirmado', $confirmed->status);
    }

    public function test_cancel_order_changes_status_to_cancelado(): void
    {
        $order = $this->service->createOrder([
            'cliente_id' => $this->clienteId,
            'items'      => [],
        ]);

        $cancelled = $this->service->cancelOrder($order->id);
        $this->assertSame('cancelado', $cancelled->status);
    }

    public function test_cancel_throws_when_already_closed(): void
    {
        $order = $this->service->createOrder([
            'cliente_id' => $this->clienteId,
            'items'      => [],
        ]);
        $order->update(['status' => 'fechado']);

        $this->expectException(RuntimeException::class);
        $this->service->cancelOrder($order->id);
    }
}
