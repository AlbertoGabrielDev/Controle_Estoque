<?php

namespace Modules\Commercial\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscountPolicyUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'               => ['sometimes', 'required', 'string', 'max:255'],
            'tipo'               => ['sometimes', 'required', 'string', 'in:item,pedido'],
            'percentual_maximo'  => ['sometimes', 'required', 'numeric', 'min:0', 'max:100'],
            'ativo'              => ['nullable', 'boolean'],
        ];
    }
}
