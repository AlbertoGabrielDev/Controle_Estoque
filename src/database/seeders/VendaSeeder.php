<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Ajuste o namespace do Model se necessário
use App\Models\Produto;

class VendaSeeder extends Seeder
{
    /**
     * Quantidade padrão de vendas a gerar.
     */
    private int $qtd = 500;

    public function run(): void
    {
        // Pegamos até 80 produtos reais, com os campos que precisamos
        $produtos = Produto::query()
            ->select(['id_produto', 'cod_produto', 'nome_produto', 'unidade_medida'])
            ->orderBy('id_produto')
            ->take(80)
            ->get()
            ->values();

        if ($produtos->isEmpty()) {
            $this->command->warn('Nenhum produto encontrado. Rode a seed de produtos antes da VendaSeeder.');
            return;
        }

        $rows = [];
        $hoje = Carbon::today();

        for ($i = 0; $i < $this->qtd; $i++) {
            $idUnidade = ($i % 2) + 1;      // 1,2,1,2,...
            $idUsuario = ($i % 3) + 1;      // 1,2,3,1,2,3,...
            $origem    = ($i % 3) + 1;      // 1,2,3,1,2,3,...

            $p = $produtos[$i % $produtos->count()];

            $diasAtras  = intdiv($i * 180, max(1, $this->qtd - 1));
            $createdAt  = $hoje->copy()->subDays($diasAtras)->setTime(rand(8, 20), rand(0, 59), rand(0, 59));
            $quantidade = rand(1, 8);

            // 🔎 Busca um estoque aleatório do mesmo produto para vincular
            $estoque = DB::table('estoques')
                ->where('id_produto_fk', $p->id_produto)
                ->inRandomOrder()
                ->select('id_estoque', 'preco_venda') // preco_venda do estoque = unitário
                ->first();

            // 💰 Define preço unitário e total da linha
            if ($estoque) {
                $precoUnit  = (float) $estoque->preco_venda;            // unitário do estoque
                $precoTotal = round($precoUnit * $quantidade, 2);       // total da venda (compatível com seu dashboard)
                $idEstoque  = (int) $estoque->id_estoque;
            } else {
                // Fallback antigo (caso não exista estoque para o produto)
                $possiveis = [4.90, 7.50, 12.90, 19.90, 29.90, 49.90, 79.90];
                $precoUnit  = (float) $possiveis[array_rand($possiveis)];
                $precoTotal = round($precoUnit * $quantidade, 2);
                $idEstoque  = null;
            }

            $rows[] = [
                'id_produto_fk'   => $p->id_produto,
                'id_usuario_fk'   => $idUsuario,
                'id_unidade_fk'   => $idUnidade,
                'id_estoque_fk'   => $idEstoque,          // ✅ novo vínculo com estoques
                'cod_produto'     => $p->cod_produto ?? ('PROD-' . str_pad((string) $p->id_produto, 4, '0', STR_PAD_LEFT)),
                'unidade_medida'  => $p->unidade_medida ?? 'un',
                'nome_produto'    => $p->nome_produto,
                'quantidade'      => $quantidade,
                'preco_venda'     => $precoTotal,         // ⚠️ total da linha (unitário * quantidade)
                'origem_venda'    => $origem,
                'created_at'      => $createdAt,
                'updated_at'      => $createdAt,
            ];
        }

        foreach (array_chunk($rows, 1000) as $chunk) {
            DB::table('vendas')->insert($chunk);
        }

        $this->command->info('VendaSeeder concluída: ' . count($rows) . ' vendas inseridas (com id_estoque_fk).');
    }
}
