<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidacaoEstoque extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'localizacao' => 'nullable|string|max:10',
            'preco_custo' => 'nullable|numeric',
            'preco_venda' => 'nullable|numeric',
            'data_chegada' => 'nullable|date',
            'validade' => 'nullable|date',
            'lote' => 'nullable|string|max:20',

            'quantidade' => 'required|numeric|min:0|lte:10000',
            'quantidade_aviso' => 'nullable|numeric|min:0|lte:10000',

           

            // >>> adicionados para permitir persistência via $request->validated()
            'imposto_total' => 'nullable|numeric',
            'impostos_json' => 'nullable|string',
        ];
    }
    public function messages(): array
    {
        return [
            'localizacao.max' => 'Máximo permitido para Localização é 10 caracteres.',
            'preco_custo.numeric' => 'Preço de Custo deve ser numérico.',
            'preco_venda.numeric' => 'Preço de Venda deve ser numérico.',
            'data_chegada.date' => 'Formato de data inválido em Data de Chegada.',
            'validade.date' => 'Formato de data inválido em Validade.',
            'lote.max' => 'Máximo permitido para Lote é 20 caracteres.',

            'quantidade.required' => 'Quantidade é obrigatória.',
            'quantidade.numeric' => 'Quantidade deve ser numérica.',
            'quantidade.min' => 'Quantidade não pode ser negativa.',
            'quantidade.lte' => 'Quantidade deve ser menor ou igual a 10000.',
            'quantidade_aviso.numeric' => 'Quantidade Alerta deve ser numérica.',
            'quantidade_aviso.min' => 'Quantidade Alerta não pode ser negativa.',
            'quantidade_aviso.lte' => 'Quantidade Alerta deve ser menor ou igual a 10000.',

           
        ];
    }
}
