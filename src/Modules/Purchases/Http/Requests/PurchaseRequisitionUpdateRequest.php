<?php

namespace Modules\Purchases\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequisitionUpdateRequest extends FormRequest
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
            'observacoes' => ['nullable', 'string'],
            'data_requisicao' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'integer', 'exists:itens,id'],
            'items.*.descricao_snapshot' => ['required', 'string', 'max:255'],
            'items.*.unidade_medida_id' => ['nullable', 'integer', 'exists:unidades_medida,id'],
            'items.*.quantidade' => ['required', 'numeric', 'min:0.001'],
            'items.*.preco_estimado' => ['nullable', 'numeric', 'min:0'],
            'items.*.imposto_id' => ['nullable', 'integer', 'exists:taxes,id'],
            'items.*.observacoes' => ['nullable', 'string'],
        ];
    }
}
