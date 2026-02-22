<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CentroCustoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $current = $this->route('centro_custo');
        $id = $current?->id ?? null;

        return [
            'codigo' => ['required', 'string', 'max:30', Rule::unique('centros_custo', 'codigo')->ignore($id)],
            'nome' => ['required', 'string', 'max:120'],
            'centro_pai_id' => ['nullable', 'integer', 'exists:centros_custo,id'],
            'ativo' => ['required', 'boolean'],
        ];
    }
}
