<?php

namespace Modules\Commercial\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Commercial\Models\CommercialOpportunity;
use Modules\Commercial\Models\CommercialProposal;
use Modules\Commercial\Models\CommercialProposalItem;
use RuntimeException;

class ProposalService
{
    public function __construct(
        private CommercialDocumentNumberService $numberService,
        private PricingPolicyService $pricingService,
    ) {
    }

    /**
     * Create a new proposal from scratch.
     *
     * @param array $payload
     * @return CommercialProposal
     * @throws \Throwable
     */
    public function createProposal(array $payload): CommercialProposal
    {
        return DB::transaction(function () use ($payload): CommercialProposal {
            $proposal = CommercialProposal::query()->create([
                'numero'          => $this->numberService->generate('PROP'),
                'opportunity_id'  => $payload['opportunity_id'] ?? null,
                'cliente_id'      => $payload['cliente_id'],
                'status'          => 'rascunho',
                'data_emissao'    => $payload['data_emissao'] ?? Carbon::today(),
                'validade_ate'    => $payload['validade_ate'] ?? null,
                'observacoes'     => $payload['observacoes'] ?? null,
                'subtotal'        => 0,
                'desconto_total'  => 0,
                'total_impostos'  => 0,
                'total'           => 0,
            ]);

            $this->syncItems($proposal, $payload['items'] ?? []);
            $this->recalcTotals($proposal);

            return $proposal->load('items');
        });
    }

    /**
     * Create a proposal from an existing opportunity.
     *
     * @param int   $opportunityId
     * @param array $payload
     * @return CommercialProposal
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function createFromOpportunity(int $opportunityId, array $payload): CommercialProposal
    {
        $opportunity = CommercialOpportunity::query()->findOrFail($opportunityId);

        if (in_array($opportunity->status, ['ganho', 'perdido'], true)) {
            throw new RuntimeException('Nao e possivel criar proposta a partir de oportunidade encerrada.');
        }

        $payload['opportunity_id'] = $opportunityId;
        $payload['cliente_id'] = $payload['cliente_id'] ?? $opportunity->cliente_id;

        return $this->createProposal($payload);
    }

    /**
     * Update a proposal in draft status.
     *
     * @param int   $proposalId
     * @param array $payload
     * @return CommercialProposal
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function updateProposal(int $proposalId, array $payload): CommercialProposal
    {
        return DB::transaction(function () use ($proposalId, $payload): CommercialProposal {
            $proposal = CommercialProposal::query()->findOrFail($proposalId);

            if ($proposal->status !== 'rascunho') {
                throw new RuntimeException('Apenas propostas em rascunho podem ser editadas.');
            }

            $proposal->update([
                'cliente_id'   => $payload['cliente_id'] ?? $proposal->cliente_id,
                'validade_ate' => $payload['validade_ate'] ?? $proposal->validade_ate,
                'observacoes'  => $payload['observacoes'] ?? $proposal->observacoes,
            ]);

            if (isset($payload['items'])) {
                $this->syncItems($proposal, $payload['items']);
            }

            $this->recalcTotals($proposal);

            return $proposal->load('items');
        });
    }

    /**
     * Mark a proposal as sent to the customer.
     *
     * @param int $proposalId
     * @return CommercialProposal
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function sendProposal(int $proposalId): CommercialProposal
    {
        return DB::transaction(function () use ($proposalId): CommercialProposal {
            $proposal = CommercialProposal::query()->findOrFail($proposalId);

            if ($proposal->status !== 'rascunho') {
                throw new RuntimeException('Apenas propostas em rascunho podem ser enviadas.');
            }

            $proposal->update(['status' => 'enviada']);

            return $proposal->fresh();
        });
    }

    /**
     * Approve a proposal.
     *
     * @param int $proposalId
     * @return CommercialProposal
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function approveProposal(int $proposalId): CommercialProposal
    {
        return DB::transaction(function () use ($proposalId): CommercialProposal {
            $proposal = CommercialProposal::query()->findOrFail($proposalId);

            if ($proposal->status !== 'enviada') {
                throw new RuntimeException('Apenas propostas enviadas podem ser aprovadas.');
            }

            $proposal->update(['status' => 'aprovada']);

            return $proposal->fresh();
        });
    }

    /**
     * Reject a proposal.
     *
     * @param int $proposalId
     * @return CommercialProposal
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function rejectProposal(int $proposalId): CommercialProposal
    {
        return DB::transaction(function () use ($proposalId): CommercialProposal {
            $proposal = CommercialProposal::query()->findOrFail($proposalId);

            if (!in_array($proposal->status, ['enviada', 'aprovada'], true)) {
                throw new RuntimeException('Apenas propostas enviadas ou aprovadas podem ser rejeitadas.');
            }

            $proposal->update(['status' => 'rejeitada']);

            return $proposal->fresh();
        });
    }

    /**
     * Mark a proposal as expired.
     *
     * @param int $proposalId
     * @return CommercialProposal
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function expireProposal(int $proposalId): CommercialProposal
    {
        return DB::transaction(function () use ($proposalId): CommercialProposal {
            $proposal = CommercialProposal::query()->findOrFail($proposalId);

            if (!in_array($proposal->status, ['rascunho', 'enviada'], true)) {
                throw new RuntimeException('Apenas propostas em rascunho ou enviadas podem ser marcadas como vencidas.');
            }

            $proposal->update(['status' => 'vencida']);

            return $proposal->fresh();
        });
    }

    /**
     * Convert an approved proposal to a sales order.
     *
     * Marks the proposal as 'convertida' and delegates order creation to SalesOrderService.
     *
     * @param int                $proposalId
     * @param SalesOrderService  $orderService
     * @param array              $extraPayload  Additional fields for the order (e.g. data_pedido).
     * @return \Modules\Commercial\Models\CommercialSalesOrder
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function convertToSalesOrder(int $proposalId, SalesOrderService $orderService, array $extraPayload = []): \Modules\Commercial\Models\CommercialSalesOrder
    {
        return DB::transaction(function () use ($proposalId, $orderService, $extraPayload) {
            $proposal = CommercialProposal::query()
                ->with('items')
                ->findOrFail($proposalId);

            if ($proposal->status !== 'aprovada') {
                throw new RuntimeException('Apenas propostas aprovadas podem ser convertidas em pedido.');
            }

            $orderPayload = array_merge([
                'proposal_id'    => $proposal->id,
                'opportunity_id' => $proposal->opportunity_id,
                'cliente_id'     => $proposal->cliente_id,
                'observacoes'    => $proposal->observacoes,
                'items'          => $proposal->items->map(fn ($i) => [
                    'item_id'            => $i->item_id,
                    'descricao_snapshot' => $i->descricao_snapshot,
                    'unidade_medida_id'  => $i->unidade_medida_id,
                    'quantidade'         => $i->quantidade,
                    'preco_unit'         => $i->preco_unit,
                    'desconto_percent'   => $i->desconto_percent,
                    'desconto_valor'     => $i->desconto_valor,
                    'imposto_id'         => $i->imposto_id,
                    'aliquota_snapshot'  => $i->aliquota_snapshot,
                    'total_linha'        => $i->total_linha,
                ])->toArray(),
            ], $extraPayload);

            $order = $orderService->createOrder($orderPayload);

            $proposal->update(['status' => 'convertida']);

            return $order;
        });
    }

    /**
     * Recalculate and persist the totals (subtotal, desconto_total, total_impostos, total) for a proposal.
     *
     * @param CommercialProposal $proposal
     * @return void
     */
    public function recalcTotals(CommercialProposal $proposal): void
    {
        $proposal->loadMissing('items');

        $subtotal       = 0;
        $descontoTotal  = 0;
        $totalImpostos  = 0;

        foreach ($proposal->items as $item) {
            $precoBase     = (float) $item->preco_unit * (float) $item->quantidade;
            $desconto      = (float) $item->desconto_valor;
            $aliquota      = (float) ($item->aliquota_snapshot ?? 0);
            $totalLinha    = $precoBase - $desconto;
            $impostoLinha  = round($totalLinha * ($aliquota / 100), 2);

            $subtotal      += $precoBase;
            $descontoTotal += $desconto;
            $totalImpostos += $impostoLinha;
        }

        $total = round($subtotal - $descontoTotal + $totalImpostos, 2);

        $proposal->update([
            'subtotal'       => round($subtotal, 2),
            'desconto_total' => round($descontoTotal, 2),
            'total_impostos' => round($totalImpostos, 2),
            'total'          => $total,
        ]);
    }

    /**
     * Sync items for a proposal: delete existing and insert new ones.
     *
     * @param CommercialProposal $proposal
     * @param array              $items
     * @return void
     */
    private function syncItems(CommercialProposal $proposal, array $items): void
    {
        $proposal->items()->delete();

        foreach ($items as $item) {
            CommercialProposalItem::query()->create([
                'proposal_id'        => $proposal->id,
                'item_id'            => $item['item_id'],
                'descricao_snapshot' => $item['descricao_snapshot'],
                'unidade_medida_id'  => $item['unidade_medida_id'] ?? null,
                'quantidade'         => $item['quantidade'],
                'preco_unit'         => $item['preco_unit'],
                'desconto_percent'   => $item['desconto_percent'] ?? 0,
                'desconto_valor'     => $item['desconto_valor'] ?? 0,
                'imposto_id'         => $item['imposto_id'] ?? null,
                'aliquota_snapshot'  => $item['aliquota_snapshot'] ?? null,
                'total_linha'        => $item['total_linha'],
            ]);
        }
    }
}
