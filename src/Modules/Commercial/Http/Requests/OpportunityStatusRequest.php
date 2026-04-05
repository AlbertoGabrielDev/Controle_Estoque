<?php

namespace Modules\Commercial\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OpportunityStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'       => ['required', 'string', 'in:novo,em_contato,proposta_enviada,negociacao,ganho,perdido'],
            'motivo_perda' => ['required_if:status,perdido', 'nullable', 'string'],
        ];
    }
}
