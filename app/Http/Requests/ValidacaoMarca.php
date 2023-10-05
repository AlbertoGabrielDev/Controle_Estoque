<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidacaoMarca extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome_marca' =>'unique:marca,nome_marca|max:20'
        ];
    }

    public function messages(): array
    {
       return[
        'nome_marca.unique' => 'Essa Marca já está cadastrada',
        'marca.max' => 'Quantidade de caractere excedida. Max:20'
       ];
    }
}
