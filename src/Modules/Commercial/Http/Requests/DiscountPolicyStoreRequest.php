<?php

namespace Modules\Commercial\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscountPolicyStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'               => ['required', 'string', 'max:255'],
            'tipo'               => ['required', 'string', 'in:item,pedido'],
            'percentual_maximo'  => ['required', 'numeric', 'min:0', 'max:100'],
            'ativo'              => ['nullable', 'boolean'],
        ];
    }
}
