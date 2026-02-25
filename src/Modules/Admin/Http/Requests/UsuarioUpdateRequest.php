<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsuarioUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('userId') ?? $this->route('user') ?? $this->route('usuario') ?? null;

        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'id_unidade' => 'required|integer|exists:unidades,id_unidade',
            'roles' => 'required|array',
            'roles.*' => 'integer|exists:roles,id',
            'password' => 'nullable|string|min:6',
            'photo' => 'nullable|image|max:4096',
        ];
    }
}
