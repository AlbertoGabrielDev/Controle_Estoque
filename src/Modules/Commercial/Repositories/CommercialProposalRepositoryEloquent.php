<?php

namespace Modules\Commercial\Repositories;

use App\Models\Item;
use App\Models\Tax;
use App\Models\UnidadeMedida;
use Illuminate\Support\Collection;
use Modules\Commercial\Models\CommercialDiscountPolicy;
use Modules\Commercial\Models\CommercialProposal;
use Modules\Customers\Models\Cliente;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class CommercialProposalRepositoryEloquent extends BaseRepository implements CommercialProposalRepository
{
    public function model()
    {
        return CommercialProposal::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
        parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    public function findWithRelations(int $id): CommercialProposal
    {
        return CommercialProposal::query()
            ->with(['opportunity', 'cliente', 'items.item', 'salesOrders'])
            ->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findForEdit(int $id): CommercialProposal
    {
        return CommercialProposal::query()
            ->with(['opportunity', 'cliente', 'items'])
            ->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function approvedOptions(): Collection
    {
        return CommercialProposal::query()
            ->select('id', 'numero', 'data_emissao', 'total', 'status')
            ->where('status', 'aprovada')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function formPayload(): array
    {
        return [
            'clientes_options' => Cliente::query()
                ->select('id_cliente', 'nome_fantasia', 'razao_social', 'nome', 'tabela_preco_id')
                ->where('ativo', true)
                ->orderBy('nome_fantasia')
                ->get(),
            'items_options' => Item::query()
                ->select('id', 'sku', 'nome', 'descricao', 'unidade_medida_id', 'preco_venda')
                ->where('ativo', true)
                ->get(),
            'unidades_options' => UnidadeMedida::query()
                ->select('id', 'codigo', 'descricao')
                ->where('ativo', true)
                ->get(),
            'impostos_options' => Tax::query()
                ->select('id', 'nome', 'aliquota')
                ->where('ativo', true)
                ->get(),
            'discount_policies_options' => CommercialDiscountPolicy::query()
                ->select('id', 'nome', 'tipo', 'percentual_maximo')
                ->where('ativo', true)
                ->get(),
        ];
    }
}
