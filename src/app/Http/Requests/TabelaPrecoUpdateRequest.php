<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TabelaPrecoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $current = $this->route('tabela_preco');
        $id = $current?->id ?? null;

        return [
            'codigo' => ['required', 'string', 'max:30', Rule::unique('tabelas_preco', 'codigo')->ignore($id)],
            'nome' => ['required', 'string', 'max:120'],
            'tipo_alvo' => ['required', 'in:item,produto'],
            'moeda' => ['required', 'string', 'max:10'],
            'inicio_vigencia' => ['nullable', 'date'],
            'fim_vigencia' => ['nullable', 'date', 'after_or_equal:inicio_vigencia'],
            'ativo' => ['required', 'boolean'],
            'itens' => ['nullable', 'array'],
            'itens.*.item_id' => ['nullable', 'integer', 'exists:itens,id'],
            'itens.*.produto_id' => ['nullable', 'integer', 'exists:produtos,id_produto'],
            'itens.*.marca_id' => ['nullable', 'integer', 'exists:marcas,id_marca'],
            'itens.*.fornecedor_id' => ['nullable', 'integer', 'exists:fornecedores,id_fornecedor'],
            'itens.*.preco' => ['required', 'numeric', 'min:0'],
            'itens.*.desconto_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'itens.*.quantidade_minima' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $tipoAlvo = $this->input('tipo_alvo', 'item');
            foreach ((array) $this->input('itens', []) as $index => $item) {
                $itemId = $item['item_id'] ?? null;
                $produtoId = $item['produto_id'] ?? null;

                if ($tipoAlvo === 'item') {
                    if (empty($itemId)) {
                        $validator->errors()->add("itens.$index.item_id", 'Selecione um item.');
                    }
                    if (!empty($produtoId)) {
                        $validator->errors()->add("itens.$index.produto_id", 'Tabela de preço é por item.');
                    }
                    if (!empty($item['marca_id'] ?? null) || !empty($item['fornecedor_id'] ?? null)) {
                        $validator->errors()->add("itens.$index.marca_id", 'Marca/Fornecedor só podem ser usados em tabela por produto.');
                    }
                } else {
                    if (empty($produtoId)) {
                        $validator->errors()->add("itens.$index.produto_id", 'Selecione um produto.');
                    }
                    if (!empty($itemId)) {
                        $validator->errors()->add("itens.$index.item_id", 'Tabela de preço é por produto.');
                    }
                }
            }
        });
    }
}
