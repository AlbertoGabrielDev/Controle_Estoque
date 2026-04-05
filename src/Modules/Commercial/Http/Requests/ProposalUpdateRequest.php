<?php

namespace Modules\Commercial\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProposalUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id'             => ['sometimes', 'required', 'integer', 'exists:clientes,id_cliente'],
            'validade_ate'           => ['nullable', 'date'],
            'observacoes'            => ['nullable', 'string'],
            'items'                  => ['sometimes', 'required', 'array', 'min:1'],
            'items.*.item_id'        => ['required', 'integer', 'exists:itens,id'],
            'items.*.descricao_snapshot' => ['required', 'string', 'max:255'],
            'items.*.unidade_medida_id'  => ['nullable', 'integer', 'exists:unidades_medida,id'],
            'items.*.quantidade'     => ['required', 'numeric', 'min:0.001'],
            'items.*.preco_unit'     => ['required', 'numeric', 'min:0'],
            'items.*.desconto_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.desconto_valor' => ['nullable', 'numeric', 'min:0'],
            'items.*.imposto_id'     => ['nullable', 'integer', 'exists:taxes,id'],
            'items.*.aliquota_snapshot' => ['nullable', 'numeric', 'min:0'],
            'items.*.total_linha'    => ['required', 'numeric', 'min:0'],
        ];
    }
}
