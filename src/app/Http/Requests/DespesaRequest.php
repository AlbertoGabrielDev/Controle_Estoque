<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DespesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data' => ['required', 'date'],
            'descricao' => ['required', 'string', 'max:255'],
            'valor' => ['required', 'numeric', 'min:0'],
            'centro_custo_id' => ['required', 'integer', 'exists:centros_custo,id'],
            'conta_contabil_id' => [
                'required',
                'integer',
                Rule::exists('contas_contabeis', 'id')
                    ->where('aceita_lancamento', 1)
                    ->where('tipo', 'despesa')
                    ->where('ativo', 1),
            ],
            'fornecedor_id' => [
                'nullable',
                'integer',
                Rule::exists('fornecedores', 'id_fornecedor')->where('ativo', 1),
            ],
            'documento' => ['nullable', 'string', 'max:60'],
            'observacoes' => ['nullable', 'string'],
            'ativo' => ['required', 'boolean'],
        ];
    }
}
