<?php

namespace Modules\Finance\Database\Seeders;

use Modules\Finance\Models\ContaContabil;
use Illuminate\Database\Seeder;

class ContaContabilSeeder extends Seeder
{
    public function run(): void
    {
        $raizes = [
            ['codigo' => '1', 'nome' => 'Ativo', 'tipo' => 'ativo'],
            ['codigo' => '2', 'nome' => 'Passivo', 'tipo' => 'passivo'],
            ['codigo' => '3', 'nome' => 'Receita', 'tipo' => 'receita'],
            ['codigo' => '4', 'nome' => 'Despesa', 'tipo' => 'despesa'],
            ['codigo' => '5', 'nome' => 'Patrimônio', 'tipo' => 'patrimonio'],
        ];

        $map = [];
        foreach ($raizes as $root) {
            $conta = ContaContabil::updateOrCreate(
                ['codigo' => $root['codigo']],
                [
                    'nome' => $root['nome'],
                    'tipo' => $root['tipo'],
                    'conta_pai_id' => null,
                    'aceita_lancamento' => false,
                    'ativo' => true,
                ]
            );
            $map[$root['codigo']] = $conta->id;
        }

        $filhas = [
            ['codigo' => '1.1', 'nome' => 'Caixa', 'tipo' => 'ativo', 'pai' => '1'],
            ['codigo' => '1.2', 'nome' => 'Bancos', 'tipo' => 'ativo', 'pai' => '1'],
            ['codigo' => '2.1', 'nome' => 'Fornecedores', 'tipo' => 'passivo', 'pai' => '2'],
            ['codigo' => '2.2', 'nome' => 'Empréstimos', 'tipo' => 'passivo', 'pai' => '2'],
            ['codigo' => '3.1', 'nome' => 'Vendas', 'tipo' => 'receita', 'pai' => '3'],
            ['codigo' => '3.2', 'nome' => 'Serviços', 'tipo' => 'receita', 'pai' => '3'],
            ['codigo' => '4.1', 'nome' => 'Custos Operacionais', 'tipo' => 'despesa', 'pai' => '4'],
            ['codigo' => '4.2', 'nome' => 'Despesas Administrativas', 'tipo' => 'despesa', 'pai' => '4'],
            ['codigo' => '5.1', 'nome' => 'Capital Social', 'tipo' => 'patrimonio', 'pai' => '5'],
        ];

        foreach ($filhas as $child) {
            ContaContabil::updateOrCreate(
                ['codigo' => $child['codigo']],
                [
                    'nome' => $child['nome'],
                    'tipo' => $child['tipo'],
                    'conta_pai_id' => $map[$child['pai']] ?? null,
                    'aceita_lancamento' => true,
                    'ativo' => true,
                ]
            );
        }
    }
}
