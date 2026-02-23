<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Produto;
use App\Models\TabelaPreco;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TabelaPrecoSeeder extends Seeder
{
    public function run(): void
    {
        $itens = Item::query()->where('ativo', true)->get();
        $produtos = Produto::query()->where('status', 1)->get();

        if ($itens->isEmpty()) {
            $this->command?->warn('Itens não encontrados. Rode ItemSeeder antes de TabelaPrecoSeeder.');
        }

        $tabelas = [
            ['codigo' => 'PADRAO', 'nome' => 'Tabela Padrão', 'moeda' => 'EUR', 'fator' => 1.00, 'desconto' => 0, 'quantidade_minima' => 1, 'tipo_alvo' => 'item'],
            ['codigo' => 'ATACADO', 'nome' => 'Tabela Atacado', 'moeda' => 'EUR', 'fator' => 0.90, 'desconto' => 5, 'quantidade_minima' => 5, 'tipo_alvo' => 'item'],
            ['codigo' => 'VIP', 'nome' => 'Tabela VIP', 'moeda' => 'EUR', 'fator' => 1.10, 'desconto' => 0, 'quantidade_minima' => 1, 'tipo_alvo' => 'produto'],
        ];

        foreach ($tabelas as $tbl) {
            $tabela = TabelaPreco::updateOrCreate(
                ['codigo' => $tbl['codigo']],
                [
                    'nome' => $tbl['nome'],
                    'tipo_alvo' => $tbl['tipo_alvo'],
                    'moeda' => $tbl['moeda'],
                    'inicio_vigencia' => Carbon::today()->subDays(10),
                    'fim_vigencia' => null,
                    'ativo' => true,
                ]
            );

            $pivot = [];

            if ($tbl['tipo_alvo'] === 'produto') {
                if ($produtos->isEmpty()) {
                    $this->command?->warn('Produtos não encontrados. Rode ProdutoSeeder antes de TabelaPrecoSeeder.');
                    continue;
                }

                foreach ($produtos as $produto) {
                    $pivot[$produto->id_produto] = [
                        'preco' => round(mt_rand(200, 10000) / 100, 2),
                        'desconto_percent' => $tbl['desconto'],
                        'quantidade_minima' => $tbl['quantidade_minima'],
                    ];
                }

                $tabela->produtos()->sync($pivot, false);
                continue;
            }

            if ($itens->isEmpty()) {
                continue;
            }

            foreach ($itens as $item) {
                $base = (float) $item->preco_base;
                if ($base <= 0) {
                    $base = round(mt_rand(200, 10000) / 100, 2);
                }

                $pivot[$item->id] = [
                    'preco' => round($base * $tbl['fator'], 2),
                    'desconto_percent' => $tbl['desconto'],
                    'quantidade_minima' => $tbl['quantidade_minima'],
                ];
            }

            $tabela->itens()->sync($pivot, false);
        }
    }
}
