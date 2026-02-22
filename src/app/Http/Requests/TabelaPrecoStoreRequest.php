<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TabelaPrecoStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:30', 'unique:tabelas_preco,codigo'],
            'nome' => ['required', 'string', 'max:120'],
            'moeda' => ['required', 'string', 'max:10'],
            'inicio_vigencia' => ['nullable', 'date'],
            'fim_vigencia' => ['nullable', 'date', 'after_or_equal:inicio_vigencia'],
            'ativo' => ['required', 'boolean'],
            'itens' => ['nullable', 'array'],
            'itens.*.item_id' => ['required', 'integer', 'exists:itens,id'],
            'itens.*.preco' => ['required', 'numeric', 'min:0'],
            'itens.*.desconto_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'itens.*.quantidade_minima' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
