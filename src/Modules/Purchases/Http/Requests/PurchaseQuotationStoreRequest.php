<?php

namespace Modules\Purchases\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseQuotationStoreRequest extends FormRequest
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
            'requisition_id' => ['required', 'integer', 'exists:purchase_requisitions,id'],
            'data_limite' => ['nullable', 'date'],
            'observacoes' => ['nullable', 'string'],
            'supplier_ids' => ['nullable', 'array'],
            'supplier_ids.*' => ['integer', 'exists:fornecedores,id_fornecedor'],
        ];
    }
}
