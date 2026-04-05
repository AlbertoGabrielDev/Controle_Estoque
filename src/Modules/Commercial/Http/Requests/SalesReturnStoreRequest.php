<?php

namespace Modules\Commercial\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesReturnStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id'                      => ['nullable', 'integer', 'exists:commercial_sales_invoices,id'],
            'order_id'                        => ['nullable', 'integer', 'exists:commercial_sales_orders,id'],
            'cliente_id'                      => ['required', 'integer', 'exists:clientes,id_cliente'],
            'motivo'                          => ['required', 'string'],
            'data_devolucao'                  => ['nullable', 'date'],
            'items'                           => ['required', 'array', 'min:1'],
            'items.*.invoice_item_id'         => ['nullable', 'integer', 'exists:commercial_sales_invoice_items,id'],
            'items.*.order_item_id'           => ['nullable', 'integer', 'exists:commercial_sales_order_items,id'],
            'items.*.item_id'                 => ['required', 'integer', 'exists:itens,id'],
            'items.*.quantidade_devolvida'    => ['required', 'numeric', 'min:0.001'],
            'items.*.observacoes'             => ['nullable', 'string'],
        ];
    }
}
