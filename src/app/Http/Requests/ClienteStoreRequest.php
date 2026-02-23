<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        if (!$this->has('ativo') && $this->has('status')) {
            $this->merge(['ativo' => (int) $this->input('status') === 1]);
        }
    }

    public function rules(): array
    {
        return [
            'codigo'      => 'nullable|string|max:30|unique:clientes,codigo',
            'tipo_pessoa' => 'required|in:PF,PJ',
            'documento'   => 'nullable|string|max:20',
            'razao_social'=> 'nullable|string|max:120',
            'nome_fantasia'=> 'nullable|string|max:120',
            'nif_cif'     => 'nullable|string|max:40',
            'nome'        => 'nullable|string|max:120',
            'email'       => 'nullable|email|max:150',
            'whatsapp'    => 'nullable|string|max:30',
            'telefone'    => 'nullable|string|max:30',
            'site'        => 'nullable|string|max:150',
            'cep'         => 'nullable|string|max:12',
            'logradouro'  => 'nullable|string|max:150',
            'numero'      => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:80',
            'bairro'      => 'nullable|string|max:80',
            'cidade'      => 'nullable|string|max:80',
            'uf'          => 'nullable|string|size:2',
            'pais'        => 'nullable|string|max:80',
            'endereco_faturacao' => 'nullable|string',
            'endereco_entrega' => 'nullable|string',
            'segment_id'  => 'nullable|exists:customer_segments,id',
            'limite_credito' => 'nullable|numeric|min:0',
            'bloqueado'   => 'boolean',
            'tabela_preco'=> 'nullable|string|max:60',
            'condicao_pagamento' => 'nullable|string|max:60',
            'tabela_preco_id' => 'nullable|integer|exists:tabelas_preco,id',
            'imposto_padrao_id' => 'nullable|integer|exists:taxes,id',
            'ativo'       => 'required|boolean',
            'status'      => 'nullable|in:0,1',
            'observacoes' => 'nullable|string',
        ];
    }
}
