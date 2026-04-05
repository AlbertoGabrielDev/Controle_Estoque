<?php

namespace Modules\Commercial\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProposalStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'opportunity_id'         => ['nullable', 'integer', 'exists:commercial_opportunities,id'],
            'cliente_id'             => ['required', 'integer', 'exists:clientes,id_cliente'],
            'data_emissao'           => ['nullable', 'date'],
            'validade_ate'           => ['nullable', 'date', 'after_or_equal:data_emissao'],
            'observacoes'            => ['nullable', 'string'],
            'items'                  => ['required', 'array', 'min:1'],
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
