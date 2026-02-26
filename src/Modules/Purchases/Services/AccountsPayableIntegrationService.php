<?php

namespace Modules\Purchases\Services;

use Illuminate\Support\Carbon;
use Modules\Purchases\Models\PurchasePayable;
use Modules\Purchases\Models\PurchaseReceipt;
use RuntimeException;

class AccountsPayableIntegrationService
{
    public function __construct(private DocumentNumberService $numberService)
    {
    }

    /**
     * Create a payable record from a receipt when allowed.
     *
     * @param int $receiptId
     * @return \Modules\Purchases\Models\PurchasePayable
     * @throws \RuntimeException
     */
    public function createPayableFromReceipt(int $receiptId): PurchasePayable
    {
        $receipt = PurchaseReceipt::query()->with('items')->findOrFail($receiptId);

        if (!in_array($receipt->status, ['conferido', 'com_divergencia'], true)) {
            throw new RuntimeException('O recebimento precisa estar conferido para gerar contas a pagar.');
        }

        $existing = PurchasePayable::query()->where('receipt_id', $receiptId)->first();
        if ($existing) {
            return $existing;
        }

        $valorTotal = $receipt->items->reduce(function (float $carry, $item): float {
            return $carry + ((float) $item->quantidade_recebida * (float) $item->preco_unit_recebido);
        }, 0.0);

        return PurchasePayable::query()->create([
            'supplier_id' => $receipt->supplier_id,
            'order_id' => $receipt->order_id,
            'receipt_id' => $receipt->id,
            'numero_documento' => $this->numberService->generate('AP'),
            'data_emissao' => $receipt->data_recebimento ?? Carbon::today(),
            'data_vencimento' => $receipt->data_recebimento ?? Carbon::today(),
            'valor_total' => $valorTotal,
            'status' => 'aberto',
        ]);
    }
}
