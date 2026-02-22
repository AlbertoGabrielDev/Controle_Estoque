<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FornecedorUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('ativo') && $this->has('status')) {
            $this->merge(['ativo' => (int) $this->input('status') === 1]);
        }
    }

    public function rules(): array
    {
        $fornecedor = $this->route('fornecedorId') ?? $this->route('fornecedor');
        $id = is_object($fornecedor) ? $fornecedor->id_fornecedor : $fornecedor;

        return [
            'codigo' => ['required', 'string', 'max:30', Rule::unique('fornecedores', 'codigo')->ignore($id, 'id_fornecedor')],
            'razao_social' => ['nullable', 'string', 'max:120'],
            'nome_fornecedor' => ['required', 'string', 'max:60'],
            'cnpj' => ['required', 'string', 'max:18', Rule::unique('fornecedores', 'cnpj')->ignore($id, 'id_fornecedor')],
            'nif_cif' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:150'],
            'telefone' => ['required', 'string', 'max:100'],
            'ddd' => ['required', 'string', 'max:2'],
            'cep' => ['required', 'string', 'max:10'],
            'logradouro' => ['required', 'string', 'max:50'],
            'bairro' => ['required', 'string', 'max:50'],
            'numero_casa' => ['required', 'string', 'max:15'],
            'cidade' => ['required', 'string', 'max:50'],
            'uf' => ['required', 'string', 'max:2'],
            'endereco' => ['nullable', 'string'],
            'prazo_entrega_dias' => ['nullable', 'integer', 'min:0'],
            'condicao_pagamento' => ['nullable', 'string', 'max:60'],
            'principal' => ['nullable', 'boolean'],
            'whatsapp' => ['nullable', 'boolean'],
            'telegram' => ['nullable', 'boolean'],
            'ativo' => ['required', 'boolean'],
            'status' => ['nullable', 'in:0,1'],
        ];
    }
}
