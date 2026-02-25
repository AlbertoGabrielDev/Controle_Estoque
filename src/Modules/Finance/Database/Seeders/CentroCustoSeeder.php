<?php

namespace Modules\Finance\Database\Seeders;

use Modules\Finance\Models\CentroCusto;
use Illuminate\Database\Seeder;

class CentroCustoSeeder extends Seeder
{
    public function run(): void
    {
        $raizes = [
            ['codigo' => 'ADM', 'nome' => 'Administrativo'],
            ['codigo' => 'COM', 'nome' => 'Comercial'],
            ['codigo' => 'LOG', 'nome' => 'Logística'],
            ['codigo' => 'TI', 'nome' => 'Tecnologia'],
        ];

        $map = [];
        foreach ($raizes as $root) {
            $centro = CentroCusto::updateOrCreate(
                ['codigo' => $root['codigo']],
                ['nome' => $root['nome'], 'centro_pai_id' => null, 'ativo' => true]
            );
            $map[$root['codigo']] = $centro->id;
        }

        $filhos = [
            ['codigo' => 'ADM-FIN', 'nome' => 'Financeiro', 'pai' => 'ADM'],
            ['codigo' => 'ADM-RH', 'nome' => 'Recursos Humanos', 'pai' => 'ADM'],
            ['codigo' => 'COM-VEN', 'nome' => 'Vendas', 'pai' => 'COM'],
            ['codigo' => 'COM-MKT', 'nome' => 'Marketing', 'pai' => 'COM'],
            ['codigo' => 'LOG-EST', 'nome' => 'Estoque', 'pai' => 'LOG'],
            ['codigo' => 'LOG-ENT', 'nome' => 'Entregas', 'pai' => 'LOG'],
            ['codigo' => 'TI-DEV', 'nome' => 'Desenvolvimento', 'pai' => 'TI'],
            ['codigo' => 'TI-INF', 'nome' => 'Infraestrutura', 'pai' => 'TI'],
        ];

        foreach ($filhos as $child) {
            CentroCusto::updateOrCreate(
                ['codigo' => $child['codigo']],
                [
                    'nome' => $child['nome'],
                    'centro_pai_id' => $map[$child['pai']] ?? null,
                    'ativo' => true,
                ]
            );
        }
    }
}
