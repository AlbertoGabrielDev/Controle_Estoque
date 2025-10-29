<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{CustomerSegment, Tax, TaxRule};

class TaxSeeder extends Seeder
{
    public function run(): void
    {
        $varejo   = CustomerSegment::firstOrCreate(['nome' => 'Varejo']);
        $atacado  = CustomerSegment::firstOrCreate(['nome' => 'Atacado']);

        $vat = Tax::firstOrCreate(['codigo' => 'VAT'], ['nome' => 'Imposto sobre Valor Agregado']);

        // Varejo 18%
        TaxRule::updateOrCreate([
            'tax_id' => $vat->id,
            'segment_id' => $varejo->id,
            'tipo_operacao' => 'venda',
        ], [
            'aliquota_percent' => 18.0000,
            'base_formula'     => 'valor_menos_desc',
            'prioridade'       => 50,
        ]);

        // Atacado 12%
        TaxRule::updateOrCreate([
            'tax_id' => $vat->id,
            'segment_id' => $atacado->id,
            'tipo_operacao' => 'venda',
        ], [
            'aliquota_percent' => 12.0000,
            'base_formula'     => 'valor_menos_desc',
            'prioridade'       => 50,
        ]);

        // Fallback 15% (sem segmento)
        TaxRule::updateOrCreate([
            'tax_id' => $vat->id,
            'segment_id' => null,
            'tipo_operacao' => 'venda',
        ], [
            'aliquota_percent' => 15.0000,
            'base_formula'     => 'valor_menos_desc',
            'prioridade'       => 100,
        ]);
    }
}
