<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImpostoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $current = $this->route('imposto');
        $id = $current?->id ?? null;

        return [
            'codigo' => ['required', 'string', 'max:20', Rule::unique('impostos', 'codigo')->ignore($id)],
            'nome' => ['required', 'string', 'max:120'],
            'tipo' => ['required', 'in:IVA,ISENTO,RETENCAO,OUTRO'],
            'aliquota_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'ativo' => ['required', 'boolean'],
        ];
    }
}
