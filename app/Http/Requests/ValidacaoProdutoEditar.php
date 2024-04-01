<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidacaoProdutoEditar extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'descricao'  => 'required|max:255',
            'inf_nutrientes'  => 'required|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'descricao.max' => 'Máximo de caracteres para o Descrição excedido. Max:60',
            'inf_nutrientes.max' => 'Máximo de caracteres para o Inf. Nutrientes excedido. Max:255',
        ];
    }
}
