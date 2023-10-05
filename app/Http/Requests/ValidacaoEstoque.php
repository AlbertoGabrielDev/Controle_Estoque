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
          'localizacao' => 'max:10',
          'preco_custo' => 'max:8',
          'preco_venda' => 'max:8',
          'data_chegada'  => 'date',
          'lote'    => 'max:20',
          
        ];
    }
    public function messages(): array
    {
        return[
            'localizacao.max' => 'Maximo de número permitido no campo Localização e 10',
            'preco_custo.max' => 'Maximo de número permitido no campo Preço Custo e 10',
            'preco_venda' => 'Maximo de número permitido no campo Preço Venda e 10',
            'data_chegada.date' => 'Formato de data errado',
            'lote' => 'Maximo de número permitido no campo Lote e 20',
        ];
    }
}
