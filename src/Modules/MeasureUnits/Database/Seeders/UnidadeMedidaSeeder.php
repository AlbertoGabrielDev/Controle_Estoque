<?php

namespace Modules\MeasureUnits\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\MeasureUnits\Models\UnidadeMedida;

class UnidadeMedidaSeeder extends Seeder
{
    public function run(): void
    {
        $bases = [
            ['codigo' => 'UN', 'descricao' => 'Unidade', 'fator_base' => 1],
            ['codigo' => 'KG', 'descricao' => 'Quilograma', 'fator_base' => 1],
            ['codigo' => 'L', 'descricao' => 'Litro', 'fator_base' => 1],
            ['codigo' => 'M', 'descricao' => 'Metro', 'fator_base' => 1],
            ['codigo' => 'H', 'descricao' => 'Hora', 'fator_base' => 1],
        ];

        foreach ($bases as $base) {
            UnidadeMedida::query()->updateOrCreate(
                ['codigo' => $base['codigo']],
                [
                    'descricao' => $base['descricao'],
                    'fator_base' => $base['fator_base'],
                    'unidade_base_id' => null,
                    'ativo' => true,
                ]
            );
        }

        $map = UnidadeMedida::query()
            ->whereIn('codigo', ['UN', 'KG', 'L', 'M', 'H'])
            ->get()
            ->keyBy('codigo');

        $derivadas = [
            ['codigo' => 'G', 'descricao' => 'Grama', 'fator_base' => 0.001, 'base' => 'KG'],
            ['codigo' => 'MG', 'descricao' => 'Miligrama', 'fator_base' => 0.000001, 'base' => 'KG'],
            ['codigo' => 'TON', 'descricao' => 'Tonelada', 'fator_base' => 1000, 'base' => 'KG'],
            ['codigo' => 'ML', 'descricao' => 'Mililitro', 'fator_base' => 0.001, 'base' => 'L'],
            ['codigo' => 'CL', 'descricao' => 'Centilitro', 'fator_base' => 0.01, 'base' => 'L'],
            ['codigo' => 'CX', 'descricao' => 'Caixa', 'fator_base' => 12, 'base' => 'UN'],
            ['codigo' => 'PCT', 'descricao' => 'Pacote', 'fator_base' => 6, 'base' => 'UN'],
            ['codigo' => 'DZ', 'descricao' => 'Duzia', 'fator_base' => 12, 'base' => 'UN'],
            ['codigo' => 'MIN', 'descricao' => 'Minuto', 'fator_base' => 0.016666, 'base' => 'H'],
        ];

        foreach ($derivadas as $derivada) {
            $base = $map[$derivada['base']] ?? null;
            UnidadeMedida::query()->updateOrCreate(
                ['codigo' => $derivada['codigo']],
                [
                    'descricao' => $derivada['descricao'],
                    'fator_base' => $derivada['fator_base'],
                    'unidade_base_id' => $base?->id,
                    'ativo' => true,
                ]
            );
        }
    }
}
