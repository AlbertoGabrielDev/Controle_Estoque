<?php

namespace Modules\Purchases\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseQuotationPricesRequest extends FormRequest
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
            'items' => ['required', 'array', 'min:1'],
            'items.*.requisition_item_id' => ['required', 'integer', 'exists:purchase_requisition_items,id'],
            'items.*.quantidade' => ['nullable', 'numeric', 'min:0.001'],
            'items.*.preco_unit' => ['required', 'numeric', 'min:0'],
            'items.*.imposto_id' => ['nullable', 'integer', 'exists:taxes,id'],
            'items.*.aliquota_snapshot' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
