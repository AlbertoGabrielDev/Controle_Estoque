<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidacaoUsuario extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'email|unique:users,email',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email já cadastrado',
            'email.email' => 'Formato de email errado'
        ];
    }
}
