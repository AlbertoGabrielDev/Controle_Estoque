<?php

namespace Modules\Units\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnidadeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
        ];
    }
}
