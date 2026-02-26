<?php

namespace Modules\Purchases\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseReceiptStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:purchase_orders,id'],
            'data_recebimento' => ['required', 'date'],
            'observacoes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.order_item_id' => ['required', 'integer', 'exists:purchase_order_items,id'],
            'items.*.quantidade_recebida' => ['required', 'numeric', 'min:0.001'],
            'items.*.preco_unit_recebido' => ['required', 'numeric', 'min:0'],
            'items.*.imposto_id' => ['nullable', 'integer', 'exists:taxes,id'],
            'items.*.aliquota_snapshot' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
