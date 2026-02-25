<?php

namespace Modules\Products\Http\Requests;

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
            'cod_produto' => 'required|string|max:60|unique:produtos,cod_produto',
            'nome_produto' => 'required|string|max:60|unique:produtos,nome_produto',
            'descricao' => 'required|string|max:255',
            'unidade_medida_id' => 'required|integer|exists:unidades_medida,id',
            'item_id' => 'nullable|integer|exists:itens,id',
            'inf_nutriente' => 'nullable|string',
            'id_categoria_fk' => 'required|integer|exists:categorias,id_categoria',
        ];
    }

    public function messages(): array
    {
        return [
            'nome_produto.required' => 'O campo "Nome do produto" e obrigatorio.',
            'nome_produto.unique' => 'O nome do produto ja esta cadastrado.',
            'cod_produto.required' => 'O campo "Codigo do produto" e obrigatorio.',
            'cod_produto.unique' => 'Codigo de produto ja cadastrado.',
            'unidade_medida_id.required' => 'Selecione uma unidade de medida.',
            'unidade_medida_id.exists' => 'Unidade de medida invalida.',
            'item_id.exists' => 'Item invalido.',
            'id_categoria_fk.required' => 'Selecione uma categoria.',
            'id_categoria_fk.exists' => 'Categoria invalida.',
        ];
    }
}
