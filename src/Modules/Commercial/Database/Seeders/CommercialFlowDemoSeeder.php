<?php

namespace Modules\Commercial\Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Commercial\Models\CommercialOpportunity;
use Modules\Commercial\Services\InvoiceService;
use Modules\Commercial\Services\OpportunityService;
use Modules\Commercial\Services\ProposalService;
use Modules\Commercial\Services\SalesOrderService;
use Modules\Commercial\Services\SalesReturnService;
use Modules\Customers\Models\Cliente;

class CommercialFlowDemoSeeder extends Seeder
{
    /**
     * Seed one complete demo flow:
     * opportunity -> proposal -> order -> invoice -> return.
     *
     * @return void
     * @throws \Throwable
     */
    public function run(): void
    {
        if (CommercialOpportunity::query()->exists()) {
            $this->command?->warn('CommercialFlowDemoSeeder skipped: commercial_opportunities already has data.');
            return;
        }

        $cliente = $this->ensureCliente();
        $itemId = $this->ensureItemId();

        $opportunityService = app(OpportunityService::class);
        $proposalService = app(ProposalService::class);
        $orderService = app(SalesOrderService::class);
        $invoiceService = app(InvoiceService::class);
        $returnService = app(SalesReturnService::class);

        $opportunity = $opportunityService->createOpportunity([
            'cliente_id' => $cliente->id_cliente,
            'nome' => 'Oportunidade Seed Comercial',
            'origem' => 'seeder',
            'valor_estimado' => 1500,
        ]);

        $proposal = $proposalService->createFromOpportunity($opportunity->id, [
            'cliente_id' => $cliente->id_cliente,
            'observacoes' => 'Proposta gerada pelo CommercialFlowDemoSeeder.',
            'items' => [[
                'item_id' => $itemId,
                'descricao_snapshot' => 'Item seed comercial',
                'quantidade' => 2,
                'preco_unit' => 75,
                'desconto_percent' => 0,
                'desconto_valor' => 0,
                'aliquota_snapshot' => 0,
                'total_linha' => 150,
            ]],
        ]);

        $proposalService->sendProposal($proposal->id);
        $proposalService->approveProposal($proposal->id);

        $order = $proposalService->convertToSalesOrder($proposal->id, $orderService);
        $orderService->confirmOrder($order->id);

        $orderItem = $order->items()->firstOrFail();

        $invoice = $invoiceService->createPartialInvoice($order->id, [[
            'order_item_id' => $orderItem->id,
            'item_id' => $itemId,
            'descricao_snapshot' => $orderItem->descricao_snapshot,
            'quantidade_faturada' => 2,
            'preco_unit' => 75,
            'desconto_percent' => 0,
            'desconto_valor' => 0,
            'aliquota_snapshot' => 0,
        ]]);

        $invoiceItem = $invoice->items()->firstOrFail();

        $salesReturn = $returnService->createReturn([
            'invoice_id' => $invoice->id,
            'order_id' => $order->id,
            'cliente_id' => $cliente->id_cliente,
            'motivo' => 'Devolucao seed para validar fluxo completo.',
            'items' => [[
                'invoice_item_id' => $invoiceItem->id,
                'order_item_id' => $orderItem->id,
                'item_id' => $itemId,
                'quantidade_devolvida' => 1,
            ]],
        ]);

        $returnService->confirmReturn($salesReturn->id);

        $this->command?->info('CommercialFlowDemoSeeder completed.');
    }

    /**
     * Ensure there is at least one active customer for demo flow seeding.
     *
     * @return Cliente
     */
    private function ensureCliente(): Cliente
    {
        $cliente = Cliente::query()
            ->where('ativo', true)
            ->first();

        if ($cliente) {
            return $cliente;
        }

        $suffix = Str::random(6);

        return Cliente::query()->create([
            'nome' => 'Cliente Comercial ' . $suffix,
            'cpf_cnpj' => '000.000.000-' . random_int(10, 99),
            'email' => 'commercial_' . $suffix . '@example.com',
            'status' => 1,
            'ativo' => true,
        ]);
    }

    /**
     * Ensure there is at least one active item and return its id.
     *
     * @return int
     */
    private function ensureItemId(): int
    {
        $item = Item::query()->where('ativo', true)->first();

        if ($item) {
            return (int) $item->id;
        }

        return (int) DB::table('itens')->insertGetId([
            'sku' => 'SEED-COM-' . Str::upper(Str::random(4)),
            'nome' => 'Item Comercial Seed',
            'ativo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

