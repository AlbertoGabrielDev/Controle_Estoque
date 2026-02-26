<?php

namespace Modules\Purchases\Services;

use Illuminate\Support\Facades\DB;
use Modules\Purchases\Models\PurchaseQuotation;
use Modules\Purchases\Models\PurchaseQuotationSupplier;
use Modules\Purchases\Models\PurchaseQuotationSupplierItem;
use Modules\Purchases\Models\PurchaseRequisition;
use Modules\Purchases\Models\PurchaseRequisitionItem;
use RuntimeException;

class PurchaseQuotationService
{
    public function __construct(private DocumentNumberService $numberService)
    {
    }

    /**
     * Create a quotation from a requisition.
     *
     * @param int $requisitionId
     * @param array $payload
     * @return \Modules\Purchases\Models\PurchaseQuotation
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function createFromRequisition(int $requisitionId, array $payload): PurchaseQuotation
    {
        return DB::transaction(function () use ($requisitionId, $payload): PurchaseQuotation {
            $requisition = PurchaseRequisition::query()->with('items')->findOrFail($requisitionId);

            $quotation = PurchaseQuotation::query()->create([
                'numero' => $this->numberService->generate('COT'),
                'status' => 'aberta',
                'requisition_id' => $requisition->id,
                'data_limite' => $payload['data_limite'] ?? null,
                'observacoes' => $payload['observacoes'] ?? null,
            ]);

            foreach ($payload['supplier_ids'] ?? [] as $supplierId) {
                $this->addSupplier($quotation->id, (int) $supplierId);
            }

            return $quotation->load('suppliers.items');
        });
    }

    /**
     * Add a supplier to an existing quotation.
     *
     * @param int $quotationId
     * @param int $supplierId
     * @return \Modules\Purchases\Models\PurchaseQuotationSupplier
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function addSupplier(int $quotationId, int $supplierId): PurchaseQuotationSupplier
    {
        return DB::transaction(function () use ($quotationId, $supplierId): PurchaseQuotationSupplier {
            $quotation = PurchaseQuotation::query()->with('requisition.items')->findOrFail($quotationId);

            if ($quotation->status !== 'aberta') {
                throw new RuntimeException('Somente cotacoes abertas podem receber fornecedores.');
            }

            $existing = PurchaseQuotationSupplier::query()
                ->where('quotation_id', $quotationId)
                ->where('supplier_id', $supplierId)
                ->first();

            if ($existing) {
                return $existing->load('items');
            }

            $supplier = PurchaseQuotationSupplier::query()->create([
                'quotation_id' => $quotationId,
                'supplier_id' => $supplierId,
                'status' => 'convidado',
            ]);

            foreach ($quotation->requisition->items as $item) {
                PurchaseQuotationSupplierItem::query()->create([
                    'quotation_supplier_id' => $supplier->id,
                    'requisition_item_id' => $item->id,
                    'item_id' => $item->item_id,
                    'quantidade' => $item->quantidade,
                    'preco_unit' => 0,
                    'imposto_id' => $item->imposto_id,
                    'aliquota_snapshot' => null,
                    'selecionado' => false,
                ]);
            }

            return $supplier->load('items');
        });
    }

    /**
     * Register supplier prices for quotation items.
     *
     * @param int $quotationSupplierId
     * @param array $items
     * @return \Modules\Purchases\Models\PurchaseQuotationSupplier
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function registerSupplierPrices(int $quotationSupplierId, array $items): PurchaseQuotationSupplier
    {
        return DB::transaction(function () use ($quotationSupplierId, $items): PurchaseQuotationSupplier {
            $supplier = PurchaseQuotationSupplier::query()->with('quotation')->findOrFail($quotationSupplierId);

            if ($supplier->quotation->status !== 'aberta') {
                throw new RuntimeException('Somente cotacoes abertas aceitam precos de fornecedores.');
            }

            foreach ($items as $itemData) {
                $requisitionItem = PurchaseRequisitionItem::query()
                    ->where('id', $itemData['requisition_item_id'])
                    ->where('requisition_id', $supplier->quotation->requisition_id)
                    ->firstOrFail();

                $supplierItem = PurchaseQuotationSupplierItem::query()
                    ->where('quotation_supplier_id', $supplier->id)
                    ->where('requisition_item_id', $requisitionItem->id)
                    ->first();

                if (!$supplierItem) {
                    $supplierItem = PurchaseQuotationSupplierItem::query()->create([
                        'quotation_supplier_id' => $supplier->id,
                        'requisition_item_id' => $requisitionItem->id,
                        'item_id' => $requisitionItem->item_id,
                        'quantidade' => $itemData['quantidade'] ?? $requisitionItem->quantidade,
                        'preco_unit' => $itemData['preco_unit'],
                        'imposto_id' => $itemData['imposto_id'] ?? $requisitionItem->imposto_id,
                        'aliquota_snapshot' => $itemData['aliquota_snapshot'] ?? null,
                        'selecionado' => false,
                    ]);
                } else {
                    $supplierItem->update([
                        'quantidade' => $itemData['quantidade'] ?? $supplierItem->quantidade,
                        'preco_unit' => $itemData['preco_unit'],
                        'imposto_id' => $itemData['imposto_id'] ?? $supplierItem->imposto_id,
                        'aliquota_snapshot' => $itemData['aliquota_snapshot'] ?? $supplierItem->aliquota_snapshot,
                    ]);
                }
            }

            $supplier->update(['status' => 'respondeu']);

            return $supplier->load('items');
        });
    }

    /**
     * Update quotation header fields.
     *
     * @param int $quotationId
     * @param array $payload
     * @return \\Modules\\Purchases\\Models\\PurchaseQuotation
     * @throws \RuntimeException
     */
    public function updateQuotation(int $quotationId, array $payload): PurchaseQuotation
    {
        $quotation = PurchaseQuotation::query()->findOrFail($quotationId);

        if ($quotation->status !== 'aberta') {
            throw new RuntimeException('Somente cotacoes abertas podem ser editadas.');
        }

        $quotation->update([
            'data_limite' => $payload['data_limite'] ?? null,
            'observacoes' => $payload['observacoes'] ?? null,
        ]);

        return $quotation->refresh();
    }

    /**
     * Select the winning supplier item for a requisition entry.
     *
     * @param int $quotationId
     * @param int $quotationSupplierItemId
     * @return \Modules\Purchases\Models\PurchaseQuotationSupplierItem
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function selectWinnerForItem(int $quotationId, int $quotationSupplierItemId): PurchaseQuotationSupplierItem
    {
        return DB::transaction(function () use ($quotationId, $quotationSupplierItemId): PurchaseQuotationSupplierItem {
            $item = PurchaseQuotationSupplierItem::query()
                ->with('quotationSupplier')
                ->findOrFail($quotationSupplierItemId);

            if ($item->quotationSupplier->quotation_id !== $quotationId) {
                throw new RuntimeException('Item informado nao pertence a cotacao.');
            }

            $quotation = PurchaseQuotation::query()->findOrFail($quotationId);

            if ($quotation->status !== 'aberta') {
                throw new RuntimeException('Somente cotacoes abertas permitem selecao de vencedor.');
            }

            PurchaseQuotationSupplierItem::query()
                ->where('requisition_item_id', $item->requisition_item_id)
                ->whereHas('quotationSupplier', function ($query) use ($quotationId) {
                    $query->where('quotation_id', $quotationId);
                })
                ->update(['selecionado' => false]);

            $item->update(['selecionado' => true]);

            return $item->refresh();
        });
    }

    /**
     * Close a quotation after validating that each item has a selected winner.
     *
     * @param int $quotationId
     * @return \Modules\Purchases\Models\PurchaseQuotation
     * @throws \RuntimeException
     */
    public function closeQuotation(int $quotationId): PurchaseQuotation
    {
        $quotation = PurchaseQuotation::query()
            ->with('requisition.items')
            ->findOrFail($quotationId);

        if ($quotation->status !== 'aberta') {
            throw new RuntimeException('Somente cotacoes abertas podem ser encerradas.');
        }

        foreach ($quotation->requisition->items as $item) {
            $selectedCount = PurchaseQuotationSupplierItem::query()
                ->where('requisition_item_id', $item->id)
                ->whereHas('quotationSupplier', function ($query) use ($quotationId) {
                    $query->where('quotation_id', $quotationId);
                })
                ->where('selecionado', true)
                ->count();

            if ($selectedCount !== 1) {
                throw new RuntimeException('Cada item deve ter exatamente um fornecedor vencedor.');
            }
        }

        $quotation->status = 'encerrada';
        $quotation->save();

        return $quotation->refresh();
    }

    /**
     * Cancel a quotation.
     *
     * @param int $quotationId
     * @return \Modules\Purchases\Models\PurchaseQuotation
     * @throws \RuntimeException
     */
    public function cancelQuotation(int $quotationId): PurchaseQuotation
    {
        $quotation = PurchaseQuotation::query()->findOrFail($quotationId);

        if ($quotation->status === 'encerrada') {
            throw new RuntimeException('Cotacoes encerradas nao podem ser canceladas.');
        }

        $quotation->status = 'cancelada';
        $quotation->save();

        return $quotation->refresh();
    }
}
