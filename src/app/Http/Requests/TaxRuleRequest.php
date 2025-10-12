<?php

namespace App\Http\Requests;

use App\Enums\Scope;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaxRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ajuste se quiser checar policies/permissions
        return true;
    }

    public function rules(): array
    {
        $ufs = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];

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
            'product_segment_id' => 'nullable|integer', // ajuste conforme PK da sua categoria

            'base' => ['required', Rule::in(['price', 'price+freight', 'subtotal'])],
            'method' => ['required', Rule::in(['percent', 'fixed', 'formula'])],

            'rate' => 'nullable|numeric|min:0|max:999.9999',
            'amount' => 'nullable|numeric|min:0|max:9999999999.99',
            'formula' => 'nullable|string|max:1000',

            'apply_mode' => ['required', Rule::in(['stack', 'exclusive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Informe um nome para a regra.',
            'method.required' => 'Selecione o método de cálculo.',
            'method.in' => 'Método inválido.',
            'rate.required_if' => 'Informe a taxa (%) quando o método for percentual.',
            'rate.numeric' => 'A taxa deve ser numérica.',
            'rate.min' => 'A taxa não pode ser negativa.',
            'rate.max' => 'A taxa não pode exceder 100%.',
            'amount.required_if' => 'Informe o valor fixo quando o método for valor.',
            'amount.numeric' => 'O valor fixo deve ser numérico.',
            'formula.required_if' => 'Informe a expressão quando o método for fórmula.',
            'formula.regex' => 'A fórmula contém caracteres inválidos.',
            'uf.in' => 'UF inválida.',
            'segment_id.exists' => 'Segmento de cliente inválido.',
            'product_segment_id.exists' => 'Segmento de produto inválido.',
            'ends_at.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
        ];
    }

    /**
     * Normaliza/“higieniza” os dados antes da validação.
     */
    protected function prepareForValidation(): void
    {
        $uf = strtoupper(trim((string) $this->input('uf', '')));
        $city = trim((string) $this->input('city', ''));

        // Booleans vindos de checkbox / "on"/"1"
        $active = filter_var($this->input('active', false), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $cumulative = filter_var($this->input('cumulative', false), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        // Números aceitando vírgula
        $rate = $this->normalizeNumber($this->input('rate'));
        $amount = $this->normalizeNumber($this->input('amount'));

        $priority = $this->input('priority');
        $priority = is_numeric($priority) ? (int) $priority : null;

        $segmentId = $this->input('segment_id');
        $segmentId = ($segmentId === '' || $segmentId === null) ? null : (int) $segmentId;

        $prodSegId = $this->input('product_segment_id');
        $prodSegId = ($prodSegId === '' || $prodSegId === null) ? null : (int) $prodSegId;

        $method = $this->input('method');

        $this->merge([
            'uf' => $uf !== '' ? $uf : null,
            'city' => $city !== '' ? $city : null,
            'active' => (bool) $active,
            'cumulative' => (bool) $cumulative,
            'rate' => $rate,
            'amount' => $amount,
            'priority' => $priority,
            'segment_id' => $segmentId,
            'product_segment_id' => $prodSegId,
            'method' => $method,
        ]);

        // Zera campos que não se aplicam ao método escolhido (evita “ghost values”)
        if ($method === 'percent') {
            $this->merge(['amount' => null, 'formula' => null]);
        } elseif ($method === 'fixed') {
            $this->merge(['rate' => null, 'formula' => null]);
        } elseif ($method === 'formula') {
            $this->merge(['rate' => null, 'amount' => null]);
        }
    }

    /**
     * Converte "10,5" para "10.5" e retorna float ou null.
     */
    private function normalizeNumber($value): ?float
    {
        if ($value === '' || $value === null) {
            return null;
        }
        if (is_string($value)) {
            $value = str_replace(['.', ','], ['', '.'], $value); // "1.234,56" -> "1234.56"
        }
        return is_numeric($value) ? (float) $value : null;
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'uf' => 'UF',
            'city' => 'cidade',
            'method' => 'método',
            'rate' => 'taxa (%)',
            'amount' => 'valor',
        ];
    }
}
