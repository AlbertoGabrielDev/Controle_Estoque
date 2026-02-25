<?php

namespace Modules\Customers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerSegmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $segmentId = $this->route('segment')?->id;

        return [
            'nome' => 'required|string|max:255|unique:customer_segments,nome,' . ($segmentId ?? 'NULL'),
        ];
    }
}
