<?php

namespace Modules\Purchases\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseReturnStoreRequest extends FormRequest
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
            'receipt_id' => ['nullable', 'integer', 'exists:purchase_receipts,id', 'required_without:order_id'],
            'order_id' => ['nullable', 'integer', 'exists:purchase_orders,id', 'required_without:receipt_id'],
            'motivo' => ['required', 'string'],
            'data_devolucao' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.receipt_item_id' => ['nullable', 'integer', 'exists:purchase_receipt_items,id'],
            'items.*.order_item_id' => ['nullable', 'integer', 'exists:purchase_order_items,id'],
            'items.*.item_id' => ['required', 'integer', 'exists:itens,id'],
            'items.*.quantidade_devolvida' => ['required', 'numeric', 'min:0.001'],
            'items.*.observacoes' => ['nullable', 'string'],
        ];
    }
}
