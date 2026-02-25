<?php

namespace Modules\Finance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContaContabilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $current = $this->route('conta_contabil');
        $id = $current?->id ?? null;

        return [
            'codigo' => ['required', 'string', 'max:30', Rule::unique('contas_contabeis', 'codigo')->ignore($id)],
            'nome' => ['required', 'string', 'max:120'],
            'tipo' => ['required', 'in:ativo,passivo,receita,despesa,patrimonio'],
            'conta_pai_id' => ['nullable', 'integer', 'exists:contas_contabeis,id'],
            'aceita_lancamento' => ['required', 'boolean'],
            'ativo' => ['required', 'boolean'],
        ];
    }
}
