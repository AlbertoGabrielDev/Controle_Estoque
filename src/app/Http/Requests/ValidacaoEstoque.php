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

        ];
    }
    public function messages(): array
    {
        return[
            'localizacao.max' => 'Maximo de número permitido no campo Localização e 10',
            'preco_custo.max' => 'Maximo de número permitido no campo Preço Custo e 10',
            'preco_venda' => 'Maximo de número permitido no campo Preço Venda e 10',
            'data_chegada.date' => 'Formato de data errado',
            'validade.date' => 'Formato de data errado',
            'quantidade.max' => 'Maximo de número permitido no campo Quantidade e 10000',
            'lote' => 'Maximo de número permitido no campo Lote e 20',
          
        ];
    }
}
