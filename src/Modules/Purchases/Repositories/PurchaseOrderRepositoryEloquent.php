<?php

namespace Modules\Purchases\Repositories;

use App\Models\Fornecedor;
use Illuminate\Support\Collection;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Models\PurchaseRequisition;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class PurchaseOrderRepositoryEloquent extends BaseRepository implements PurchaseOrderRepository
{
    public function model()
    {
        return PurchaseOrder::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
        parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    public function findWithRelations(int $id): PurchaseOrder
    {
        return PurchaseOrder::query()
            ->with(['items', 'receipts', 'supplier', 'quotation', 'requisition', 'returns', 'payables'])
            ->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByIdWithItems(int $id): PurchaseOrder
    {
        return PurchaseOrder::query()
            ->with('items')
            ->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function requisitionsOptions(): Collection
    {
        return PurchaseRequisition::query()
            ->select('id', 'numero', 'data_requisicao', 'observacoes', 'status')
            ->where('status', 'LIKE', '%aprovado%')
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function suppliersOptions(): Collection
    {
        return Fornecedor::query()
            ->select('id_fornecedor as id', 'razao_social', 'nome_fornecedor', 'cnpj')
            ->where('ativo', true)
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableForReceipt(): Collection
    {
        return PurchaseOrder::query()
            ->select('id', 'numero', 'status', 'data_emissao')
            ->with([
                'items' => function ($q) {
                    $q->select('id', 'order_id', 'item_id', 'descricao_snapshot', 'quantidade_pedida', 'preco_unit', 'quantidade_recebida');
                }
            ])
            ->whereIn('status', ['emitido', 'parcialmente_recebido'])
            ->get();
    }
}
