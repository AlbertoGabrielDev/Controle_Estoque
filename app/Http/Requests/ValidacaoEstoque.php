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
        'localizacao'       => 'max:10',
        'preco_custo'       => 'max:8',
        'preco_venda'       => 'max:8',
        'data_chegada'      => 'date',
        'quantidade'        => 'max:10000',
        'validade'          => 'date',
        'lote'              => 'max:20',
        'id_fornecedor_fk'  => 'required|exists:fornecedor,id_fornecedor',
        'id_marca_fk'       => 'required|exists:marca,id_marca',
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
            'id_fornecedor_fk.required' => 'O fornecedor é obrigatório',
            'id_fornecedor_fk.exists' => 'Fornecedor inválido',
            'id_marca_fk.required' => 'A marca é obrigatória',
            'id_marca_fk.exists' => 'Marca inválida',
        ];
    }
}
