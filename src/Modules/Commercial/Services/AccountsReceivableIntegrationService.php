<?php

namespace Modules\Commercial\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Commercial\Models\CommercialSalesInvoice;
use Modules\Commercial\Models\CommercialSalesReceivable;

/**
 * Handles the integration between invoices/returns and the accounts receivable ledger.
 *
 * NOTE: The Finance module does not have an accounts receivable structure.
 * This service therefore manages receivables within the Commercial module
 * using the commercial_sales_receivables table.
 * When a proper Finance AR module is implemented, this service should be
 * refactored to delegate to that module instead.
 */
class AccountsReceivableIntegrationService
{
    public function __construct(private CommercialDocumentNumberService $numberService)
    {
    }

    /**
     * Create an accounts receivable record from a newly issued invoice.
     *
     * @param CommercialSalesInvoice $invoice
     * @return CommercialSalesReceivable
     * @throws \Throwable
     */
    public function createReceivableFromInvoice(CommercialSalesInvoice $invoice): CommercialSalesReceivable
    {
        return DB::transaction(function () use ($invoice): CommercialSalesReceivable {
            $invoice->loadMissing('order');

            return CommercialSalesReceivable::query()->create([
                'numero_documento' => $this->numberService->generate('AR'),
                'invoice_id'       => $invoice->id,
                'order_id'         => $invoice->order_id,
                'cliente_id'       => $invoice->cliente_id,
                'data_emissao'     => $invoice->data_emissao ?? Carbon::today(),
                'data_vencimento'  => $invoice->data_vencimento ?? Carbon::today()->addDays(30),
                'valor_total'      => $invoice->total,
                'status'           => 'aberto',
            ]);
        });
    }

    /**
     * Reverse (cancel or mark as estornado) a collection of receivable records.
     *
     * Used when a return is confirmed or an invoice is cancelled.
     *
     * @param Collection $receivables  CommercialSalesReceivable instances to reverse.
     * @return void
     * @throws \Throwable
     */
    public function reverseReceivableFromReturn(Collection $receivables): void
    {
        DB::transaction(function () use ($receivables): void {
            foreach ($receivables as $receivable) {
                if (in_array($receivable->status, ['cancelado', 'estornado'], true)) {
                    continue;
                }

                $receivable->update(['status' => 'estornado']);
            }
        });
    }
}
