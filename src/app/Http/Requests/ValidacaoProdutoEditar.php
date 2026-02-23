<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidacaoProdutoEditar extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $produtoId = (int) $this->route('produtoId');

        return [
            'cod_produto' => [
                'required',
                'string',
                'max:60',
                Rule::unique('produtos', 'cod_produto')->ignore($produtoId, 'id_produto'),
            ],
            'nome_produto' => [
                'required',
                'string',
                'max:60',
                Rule::unique('produtos', 'nome_produto')->ignore($produtoId, 'id_produto'),
            ],
            'descricao' => 'required|string|max:255',
            'unidade_medida_id' => 'required|integer|exists:unidades_medida,id',
            'item_id' => 'nullable|integer|exists:itens,id',
            'inf_nutriente' => 'nullable|string',
            'qrcode' => 'nullable|string|max:255',
            'id_categoria_fk' => 'required|integer|exists:categorias,id_categoria',
        ];
    }

    public function messages(): array
    {
        return [
            'nome_produto.required' => 'O campo "Nome do produto" é obrigatório.',
            'nome_produto.unique' => 'O nome do produto já está cadastrado.',
            'cod_produto.required' => 'O campo "Código do produto" é obrigatório.',
            'cod_produto.unique' => 'Código de produto já cadastrado.',
            'unidade_medida_id.required' => 'Selecione uma unidade de medida.',
            'unidade_medida_id.exists' => 'Unidade de medida inválida.',
            'item_id.exists' => 'Item inválido.',
            'id_categoria_fk.required' => 'Selecione uma categoria.',
            'id_categoria_fk.exists' => 'Categoria inválida.',
        ];
    }
}
