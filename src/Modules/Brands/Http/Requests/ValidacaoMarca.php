<?php

namespace Modules\Brands\Http\Requests;

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
            'nome_marca' => ['required', 'max:20', 'unique:marcas,nome_marca'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome_marca.required' => 'Informe a marca.',
            'nome_marca.unique' => 'Essa marca ja esta cadastrada.',
            'nome_marca.max' => 'Quantidade de caracteres excedida. Max:20.',
        ];
    }
}
