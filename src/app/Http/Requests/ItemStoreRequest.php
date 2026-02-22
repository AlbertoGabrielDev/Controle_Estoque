<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:60', 'unique:itens,sku'],
            'nome' => ['required', 'string', 'max:120'],
            'tipo' => ['required', 'in:produto,servico'],
            'categoria_id' => ['nullable', 'integer', 'exists:categorias,id_categoria'],
            'unidade_medida_id' => ['nullable', 'integer', 'exists:unidades_medida,id'],
            'descricao' => ['nullable', 'string'],
            'custo' => ['nullable', 'numeric', 'min:0'],
            'preco_base' => ['nullable', 'numeric', 'min:0'],
            'peso_kg' => ['nullable', 'numeric', 'min:0'],
            'volume_m3' => ['nullable', 'numeric', 'min:0'],
            'controla_estoque' => ['required', 'boolean'],
            'ativo' => ['required', 'boolean'],
        ];
    }
}
