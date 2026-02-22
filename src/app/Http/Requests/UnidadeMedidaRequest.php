<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnidadeMedidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $current = $this->route('unidade_medida');
        $id = $current?->id ?? null;

        return [
            'codigo' => ['required', 'string', 'max:10', Rule::unique('unidades_medida', 'codigo')->ignore($id)],
            'descricao' => ['required', 'string', 'max:120'],
            'fator_base' => ['nullable', 'numeric', 'min:0'],
            'unidade_base_id' => ['nullable', 'integer', 'exists:unidades_medida,id'],
            'ativo' => ['required', 'boolean'],
        ];
    }
}
