<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidacaoProduto extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'nome_produto' => 'required|unique:produto,nome_produto|max:60',
            'descricao'  => 'required|max:255',
            'unidade_medida'  => 'required',
            'cod_produto'  => 'required|unique:produto,cod_produto|max:60',
            'inf_nutrientes'  => 'required|max:255',
            'nome_categoria'  => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'nome_produto.required' =>'O campo "Nome do produto" é obrigatorio',
            'nome_produto.unique' => 'O nome do produto já está cadastrado',
            'nome_produto.max' => 'Máximo de caracteres excedido',
            'cod_produto.unique' => 'Código de Produto já cadastrado'
        ];
    }
}
