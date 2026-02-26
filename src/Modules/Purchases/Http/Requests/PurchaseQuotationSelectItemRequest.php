<?php

namespace Modules\Purchases\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseQuotationSelectItemRequest extends FormRequest
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
            'quotation_supplier_item_id' => ['required', 'integer', 'exists:purchase_quotation_supplier_items,id'],
        ];
    }
}
