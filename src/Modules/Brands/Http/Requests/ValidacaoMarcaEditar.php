<?php

namespace Modules\Brands\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidacaoMarcaEditar extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $routeId = (int) $this->route('marcaId');

        return [
            'nome_marca' => [
                'required',
                'max:20',
                Rule::unique('marcas', 'nome_marca')->ignore($routeId, 'id_marca'),
            ],
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
