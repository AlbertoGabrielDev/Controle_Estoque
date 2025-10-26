<?php

namespace App\Http\Requests;

use App\Enums\Canal;
use App\Enums\TipoOperacao;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaxRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'tax_code' => 'required|string|max:30',
            'scope' => ['required', 'integer', Rule::in([1, 2, 3])],
            'priority' => 'nullable|integer|min:0|max:100000',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'origin_uf' => 'nullable|string|size:2',
            'dest_uf' => 'nullable|string|size:2',
            'customer_segment_id' => 'nullable|integer|exists:customer_segments,id',
            'product_segment_id' => 'nullable|integer',
            'base' => ['required', Rule::in(['price', 'price+freight', 'subtotal'])],
            'method' => ['required', Rule::in(['percent', 'fixed', 'formula'])],
            'rate' => ['nullable', 'decimal:0,4', 'gte:0', 'lte:100', 'required_if:method,percent'],
            'amount' => ['nullable', 'decimal:0,2', 'gte:0', 'lte:9999999999.99'],
            'formula' => 'nullable|string|max:1000',
            'canal' => ['nullable', Rule::enum(Canal::class)],
            'tipo_operacao' => ['nullable', Rule::enum(TipoOperacao::class)],
            'apply_mode' => ['required', Rule::in(['stack', 'exclusive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Informe um nome para a regra.',
            'tax_code.required' => 'Informe o código do imposto.',
            'method.required' => 'Selecione o método de cálculo.',
            'method.in' => 'Método inválido.',
            'rate.required_if' => 'Informe a taxa (%) quando o método for percentual.',
            'rate.decimal' => 'A taxa deve ter no máximo 4 casas decimais.',
            'rate.gte' => 'A taxa não pode ser negativa.',
            'rate.lte' => 'A taxa não pode exceder 100%.',
            'amount.decimal' => 'O valor fixo deve ter no máximo 2 casas decimais.',
            'amount.gte' => 'O valor fixo não pode ser negativo.',
            'origin_uf.size' => 'UF de origem deve ter 2 letras.',
            'dest_uf.size' => 'UF de destino deve ter 2 letras.',
            'customer_segment_id.exists' => 'Segmento de cliente inválido.',
            'product_segment_id.exists' => 'Segmento de produto inválido.',
            'ends_at.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $normalizeNumber = function ($value) {
            if ($value === '' || $value === null)
                return null;

            $value = is_string($value) ? trim($value) : $value;

            if (is_string($value)) {
                if (str_contains($value, ',')) {
                    $value = str_replace(['.', ','], ['', '.'], $value);
                } else {
                    $value = str_replace(' ', '', $value);
                }
            }

            return is_numeric($value) ? $value : null;
        };

        $normalizeNullable = fn($v) => ($v === '' ? null : $v);

        $this->merge([
            'rate' => $normalizeNumber($this->input('rate')),
            'amount' => $normalizeNumber($this->input('amount')),
            'origin_uf' => $normalizeNullable(strtoupper((string) $this->input('origin_uf', ''))),
            'dest_uf' => $normalizeNullable(strtoupper((string) $this->input('dest_uf', ''))),
            'customer_segment_id' => $normalizeNullable($this->input('customer_segment_id')),
            'product_segment_id' => $normalizeNullable($this->input('product_segment_id')),
            'canal' => ($v = $this->input('canal')) ? strtolower($v) : null,              // <<<
            'tipo_operacao' => ($v = $this->input('tipo_operacao')) ? strtolower($v) : null,
        ]);

        $method = $this->input('method');
        if ($method === 'percent') {
            $this->merge(['amount' => null, 'formula' => null]);
        } elseif ($method === 'fixed') {
            $this->merge(['rate' => null, 'formula' => null]);
        } elseif ($method === 'formula') {
            $this->merge(['amount' => null]);
        }
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'tax_code' => 'código do imposto',
            'method' => 'método',
            'rate' => 'taxa (%)',
            'amount' => 'valor',
            'origin_uf' => 'UF de origem',
            'dest_uf' => 'UF de destino',
        ];
    }
}
