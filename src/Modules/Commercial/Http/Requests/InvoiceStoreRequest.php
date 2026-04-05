<?php

namespace Modules\Commercial\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id'               => ['required', 'integer', 'exists:commercial_sales_orders,id'],
            'data_emissao'           => ['nullable', 'date'],
            'data_vencimento'        => ['nullable', 'date'],
            'observacoes'            => ['nullable', 'string'],
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.order_item_id'  => ['required', 'integer', 'exists:commercial_sales_order_items,id'],
            'items.*.item_id'        => ['required', 'integer', 'exists:itens,id'],
            'items.*.descricao_snapshot' => ['required', 'string', 'max:255'],
            'items.*.quantidade_faturada' => ['required', 'numeric', 'min:0.001'],
            'items.*.preco_unit'     => ['required', 'numeric', 'min:0'],
            'items.*.desconto_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.desconto_valor' => ['nullable', 'numeric', 'min:0'],
            'items.*.imposto_id'     => ['nullable', 'integer', 'exists:taxes,id'],
            'items.*.aliquota_snapshot' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
