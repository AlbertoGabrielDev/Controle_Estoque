<?php

namespace Modules\Commercial\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OpportunityStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id'               => ['nullable', 'integer', 'exists:clientes,id_cliente'],
            'nome'                     => ['required', 'string', 'max:255'],
            'descricao'                => ['nullable', 'string'],
            'origem'                   => ['nullable', 'string', 'max:100'],
            'responsavel_id'           => ['nullable', 'integer', 'exists:users,id'],
            'valor_estimado'           => ['nullable', 'numeric', 'min:0'],
            'data_prevista_fechamento' => ['nullable', 'date'],
            'observacoes'              => ['nullable', 'string'],
        ];
    }
}
