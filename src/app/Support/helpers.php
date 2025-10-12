<?php

if (! function_exists('current_unidade')) {
    /**
     * Retorna a unidade atual (ou null).
     * Preenchida pelo middleware SyncUnidade e registrada no container.
     */
    function current_unidade()
    {
        return app()->bound('current.unidade') ? app('current.unidade') : null;
    }
}

function uiBaseToDb(string $base): string
{
    return match ($base) {
        'price'         => 'valor_menos_desc',
        'price+freight' => 'valor_mais_frete',
        'subtotal'      => 'valor',
        default         => 'valor_menos_desc',
    };
}

function uiMethodToInt(string $method): int
{
    return match ($method) {
        'percent' => \App\Enums\TaxMethod::Percent->value,
        'fixed'   => \App\Enums\TaxMethod::Fixed->value,
        'formula' => \App\Enums\TaxMethod::Formula->value,
        default   => \App\Enums\TaxMethod::Percent->value,
    };
}

function resolveTaxId(array $data): int
{
    $code = trim($data['tax_code'] ?? '');
    $name = trim($data['name'] ?? $code);
    if ($code === '') abort(422, 'Código do imposto é obrigatório.');

    $tax = \App\Models\Tax::firstOrCreate(['codigo'=>$code], ['nome'=>$name,'ativo'=>true]);
    // opcional: atualizar nome se mudou
    if ($name && $tax->nome !== $name) { $tax->nome = $name; $tax->save(); }

    return $tax->id;
}