<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidacaoEstoque extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $estoqueId = $this->route('estoqueId');
        return [
            'id_produto_fk' => 'required|integer|exists:produtos,id_produto',
            'id_fornecedor_fk' => 'required|integer|exists:fornecedores,id_fornecedor',
            'id_marca_fk' => 'required|integer|exists:marcas,id_marca',
            'localizacao' => 'nullable|string|max:10',
            'preco_custo' => 'nullable|numeric',
            'preco_venda' => 'nullable|numeric',
            'data_chegada' => 'nullable|date',
            'validade' => 'nullable|date',
            'lote' => 'nullable|string|max:20',
            'qrcode' => [
                'nullable',
                'string',
                'max:80',
                Rule::unique('estoques', 'qrcode')->ignore($estoqueId, 'id_estoque'),
            ],
            'quantidade' => 'required|numeric|min:0|lte:10000',
            'quantidade_aviso' => 'nullable|numeric|min:0|lte:10000',
        ];
    }

    public function messages(): array
    {
        return [
            'id_produto_fk.required' => 'Selecione um produto.',
            'id_fornecedor_fk.required' => 'Selecione um fornecedor.',
            'id_marca_fk.required' => 'Selecione uma marca.',
            'localizacao.max' => 'Máximo de número permitido no campo Localização é 10.',
            'data_chegada.date' => 'Formato de data inválido.',
            'validade.date' => 'Formato de data inválido.',
            'quantidade.lte' => 'Quantidade máxima permitida é 10000.',
            'quantidade_aviso.lte' => 'Quantidade de aviso máxima permitida é 10000.',
            'lote.max' => 'Máximo de caracteres para lote é 20.',
            'qrcode.unique' => 'QRCode já está em uso.',
        ];
    }
}
