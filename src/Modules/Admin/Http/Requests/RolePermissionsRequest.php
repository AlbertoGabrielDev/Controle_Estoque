<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RolePermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'global_permissions.status' => 'nullable|boolean',
            'global_permissions.non_crud' => 'nullable|array',
            'global_permissions.non_crud.*' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'array',
            'permissions.*.*' => 'integer|exists:permissions,id',
        ];
    }
}
