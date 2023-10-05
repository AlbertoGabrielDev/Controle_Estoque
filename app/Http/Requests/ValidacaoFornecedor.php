<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidacaoFornecedor extends FormRequest
{
 
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome_fornecedor' => 'unique:fornecedor,nome_fornecedor|max:60',
            'cnpj' => 'unique:fornecedor,cnpj|max:18|min:14',
            'cep' => 'max:10|min:8',
            'email' =>'email|max:60',
            'logradouro' => 'max:50',
            'bairro' => 'max:50',
            'numero_casa' => 'max:15',
            'cidade' => 'max:50',
            'uf' => 'max:2',
        ];
    }

    public function messages(){
        return [
            'nome_fornecedor.unique' => 'Esse nome de fornecedor já está cadastrado',
            'nome_fornecedor.max' => 'Máximo de caracteres para nome excedido',
            'cnpj.unique' => 'Esse cnpj já está em uso no sistema',
            'cnpj.max' => 'Número de cnpj não pode ultrapassar 18',
            'cnpj.min' => 'Número de cnpj não pode ser menor que 14',
            'cep.max'  => 'Número de cep não pode ultrapassar 10',
            'cep.min' => 'Número de cep não pode ser menor que 8',
            'email.email' => 'Esse email não e valido',
            'email.max' => 'Máximo de caracteres para email excedido. Max:60',
            'logradouro.max' => 'Máximo de caracteres para logradouro excedido. Max:50',
            'bairro.max' => 'Máximo de caracteres para bairro excedido. Max:50',
            'numero_casa.max' => 'Número de casa não pode ser menor que 15',
            'cidade.max' => 'Máximo de caracteres para cidade excedido. Max:50',
            'uf.max' => 'Máximo de caracteres para uf excedido. Max:2'
        ];
    }
}
