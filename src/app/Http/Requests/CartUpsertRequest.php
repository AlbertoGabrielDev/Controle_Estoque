<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartUpsertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client' => ['required', 'string', 'max:20'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sku' => ['required', 'string', 'max:60'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ];
    }
}
