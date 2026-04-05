<?php

namespace Modules\Commercial\Services;

use Illuminate\Support\Facades\DB;
use Modules\Commercial\Models\CommercialOpportunity;
use RuntimeException;

class OpportunityService
{
    public function __construct(private CommercialDocumentNumberService $numberService)
    {
    }

    /**
     * Create a new commercial opportunity.
     *
     * @param array $payload
     * @return CommercialOpportunity
     * @throws \Throwable
     */
    public function createOpportunity(array $payload): CommercialOpportunity
    {
        return DB::transaction(function () use ($payload): CommercialOpportunity {
            return CommercialOpportunity::query()->create([
                'codigo'                   => $this->numberService->generate('OPP'),
                'cliente_id'               => $payload['cliente_id'] ?? null,
                'nome'                     => $payload['nome'],
                'descricao'                => $payload['descricao'] ?? null,
                'origem'                   => $payload['origem'] ?? null,
                'responsavel_id'           => $payload['responsavel_id'] ?? null,
                'status'                   => 'novo',
                'valor_estimado'           => $payload['valor_estimado'] ?? 0,
                'data_prevista_fechamento' => $payload['data_prevista_fechamento'] ?? null,
                'observacoes'              => $payload['observacoes'] ?? null,
            ]);
        });
    }

    /**
     * Update an existing opportunity.
     *
     * @param int   $id
     * @param array $payload
     * @return CommercialOpportunity
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function updateOpportunity(int $id, array $payload): CommercialOpportunity
    {
        return DB::transaction(function () use ($id, $payload): CommercialOpportunity {
            $opportunity = CommercialOpportunity::query()->findOrFail($id);

            if (in_array($opportunity->status, ['ganho', 'perdido'], true)) {
                throw new RuntimeException('Oportunidades encerradas nao podem ser editadas.');
            }

            $opportunity->update([
                'cliente_id'               => $payload['cliente_id'] ?? $opportunity->cliente_id,
                'nome'                     => $payload['nome'] ?? $opportunity->nome,
                'descricao'                => $payload['descricao'] ?? $opportunity->descricao,
                'origem'                   => $payload['origem'] ?? $opportunity->origem,
                'responsavel_id'           => $payload['responsavel_id'] ?? $opportunity->responsavel_id,
                'valor_estimado'           => $payload['valor_estimado'] ?? $opportunity->valor_estimado,
                'data_prevista_fechamento' => $payload['data_prevista_fechamento'] ?? $opportunity->data_prevista_fechamento,
                'observacoes'              => $payload['observacoes'] ?? $opportunity->observacoes,
            ]);

            return $opportunity->fresh();
        });
    }

    /**
     * Advance the opportunity to a new status.
     *
     * @param int    $id
     * @param string $newStatus
     * @return CommercialOpportunity
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function updateStatus(int $id, string $newStatus): CommercialOpportunity
    {
        return DB::transaction(function () use ($id, $newStatus): CommercialOpportunity {
            $opportunity = CommercialOpportunity::query()->findOrFail($id);

            if (in_array($opportunity->status, ['ganho', 'perdido'], true)) {
                throw new RuntimeException('Oportunidade ja encerrada e nao pode ter o status alterado.');
            }

            $opportunity->update(['status' => $newStatus]);

            return $opportunity->fresh();
        });
    }

    /**
     * Mark an opportunity as won.
     *
     * @param int $id
     * @return CommercialOpportunity
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function markAsWon(int $id): CommercialOpportunity
    {
        return DB::transaction(function () use ($id): CommercialOpportunity {
            $opportunity = CommercialOpportunity::query()->findOrFail($id);

            if ($opportunity->status === 'perdido') {
                throw new RuntimeException('Uma oportunidade perdida nao pode ser marcada como ganha.');
            }

            $opportunity->update(['status' => 'ganho']);

            return $opportunity->fresh();
        });
    }

    /**
     * Mark an opportunity as lost, requiring a reason.
     *
     * @param int    $id
     * @param string $motivoPerda
     * @return CommercialOpportunity
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function markAsLost(int $id, string $motivoPerda): CommercialOpportunity
    {
        if (empty(trim($motivoPerda))) {
            throw new RuntimeException('Motivo da perda e obrigatorio.');
        }

        return DB::transaction(function () use ($id, $motivoPerda): CommercialOpportunity {
            $opportunity = CommercialOpportunity::query()->findOrFail($id);

            if ($opportunity->status === 'ganho') {
                throw new RuntimeException('Uma oportunidade ganha nao pode ser marcada como perdida.');
            }

            $opportunity->update([
                'status'       => 'perdido',
                'motivo_perda' => $motivoPerda,
            ]);

            return $opportunity->fresh();
        });
    }

    /**
     * Convert an opportunity to a proposal, updating its status to 'proposta_enviada'.
     *
     * The actual proposal creation is handled by ProposalService::createFromOpportunity.
     * This method only marks the opportunity as having a proposal.
     *
     * @param int $id
     * @return CommercialOpportunity
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function convertToProposal(int $id): CommercialOpportunity
    {
        return DB::transaction(function () use ($id): CommercialOpportunity {
            $opportunity = CommercialOpportunity::query()->findOrFail($id);

            if (in_array($opportunity->status, ['ganho', 'perdido'], true)) {
                throw new RuntimeException('Oportunidade encerrada nao pode gerar proposta.');
            }

            $opportunity->update(['status' => 'proposta_enviada']);

            return $opportunity->fresh();
        });
    }
}
