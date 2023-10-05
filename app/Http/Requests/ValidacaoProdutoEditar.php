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
            'nome_produto' => 'max:60',
            'descricao'  => 'required|max:255',
            'validade'  => 'required',
            'unidade_medida'  => 'required',
            'cod_produto'  => 'max:60',
            'inf_nutrientes'  => 'required|max:255',
            'nome_categoria'  => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'nome_produto.max' => 'Máximo de caracteres para o nome excedido. Max:60',
            'descricao.max' => 'Máximo de caracteres para o Descrição excedido. Max:60',
            'cod_produto.max' => 'CMáximo de caracteres para o Cod. Produto excedido. Max:60',
            'inf_nutrientes.max' => 'Máximo de caracteres para o Inf. Nutrientes excedido. Max:255',
        ];
    }
}
