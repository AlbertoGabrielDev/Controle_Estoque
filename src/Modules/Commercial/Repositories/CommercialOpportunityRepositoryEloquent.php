<?php

namespace Modules\Commercial\Repositories;

use App\Models\UnidadeMedida;
use Illuminate\Support\Collection;
use Modules\Commercial\Models\CommercialOpportunity;
use Modules\Customers\Models\Cliente;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class CommercialOpportunityRepositoryEloquent extends BaseRepository implements CommercialOpportunityRepository
{
    public function model()
    {
        return CommercialOpportunity::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
        parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    public function findWithRelations(int $id): CommercialOpportunity
    {
        return CommercialOpportunity::query()
            ->with(['cliente', 'responsavel', 'proposals', 'salesOrders'])
            ->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findForEdit(int $id): CommercialOpportunity
    {
        return CommercialOpportunity::query()
            ->with(['cliente', 'responsavel'])
            ->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function openOptions(): Collection
    {
        return CommercialOpportunity::query()
            ->select('id', 'codigo', 'nome', 'status', 'valor_estimado')
            ->whereNotIn('status', ['ganho', 'perdido'])
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
                ->select('id_cliente', 'nome_fantasia', 'razao_social', 'nome')
                ->where('ativo', true)
                ->orderBy('nome_fantasia')
                ->get(),
            'users_options' => \App\Models\User::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
        ];
    }
}
