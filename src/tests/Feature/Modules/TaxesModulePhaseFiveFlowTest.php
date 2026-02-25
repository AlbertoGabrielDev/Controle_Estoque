<?php

namespace Tests\Feature\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Taxes\Models\Tax;
use Modules\Taxes\Models\TaxRule;
use Modules\Taxes\Services\TaxCalculatorService;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class TaxesModulePhaseFiveFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_tax_calculator_applies_basic_rule(): void
    {
        $tax = Tax::query()->create([
            'codigo' => 'VAT',
            'nome' => 'VAT',
            'ativo' => true,
        ]);

        TaxRule::query()->create([
            'tax_id' => $tax->id,
            'escopo' => 1,
            'metodo' => 1,
            'aliquota_percent' => 10.0,
            'base_formula' => 'valor_menos_desc',
            'prioridade' => 10,
            'cumulativo' => false,
            'tipo_operacao' => 'venda',
        ]);

        $service = app(TaxCalculatorService::class);
        $result = $service->calcular([
            'valores' => [
                'valor' => 100,
                'desconto' => 0,
                'frete' => 0,
            ],
            'operacao' => [
                'tipo' => 'venda',
            ],
        ]);

        $this->assertSame(10.0, (float) ($result['_total_impostos'] ?? 0));
        $this->assertSame(110.0, (float) ($result['_total_com_impostos'] ?? 0));
        $this->assertContains($tax->id, $result['_applied_tax_ids'] ?? []);
    }
}
