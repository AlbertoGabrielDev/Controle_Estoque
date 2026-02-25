<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'id_unidade' => 'required|integer|exists:unidades,id_unidade',
            'roles' => 'required|array',
            'roles.*' => 'integer|exists:roles,id',
            'photo' => 'nullable|image|max:4096',
        ];
    }
}
