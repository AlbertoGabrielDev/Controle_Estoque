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
            $origem = ($i % 3) + 1;      // 1,2,3,1,2,3,...

            $p = $produtos[$i % $produtos->count()];

            $diasAtras = intdiv($i * 180, max(1, $this->qtd - 1));
            $createdAt = $hoje->copy()->subDays($diasAtras)->setTime(rand(8, 20), rand(0, 59), rand(0, 59));

            $quantidade = rand(1, 8);
            $precoUnit = round([4.90, 7.50, 12.90, 19.90, 29.90, 49.90, 79.90][array_rand([0, 1, 2, 3, 4, 5, 6])], 2);
            $precoVenda = round($precoUnit * $quantidade, 2);

            $rows[] = [
                'id_produto_fk' => $p->id_produto,
                'id_usuario_fk' => $idUsuario,
                'cod_produto' => $p->cod_produto ?? ('PROD-' . str_pad((string) $p->id_produto, 4, '0', STR_PAD_LEFT)),
                'unidade_medida' => $p->unidade_medida ?? 'un',
                'nome_produto' => $p->nome_produto,
                'quantidade' => $quantidade,
                'preco_venda' => $precoVenda,
                'id_unidade_fk' => $idUnidade,
                'origem_venda' => $origem,   // <- agora 1/2/3
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }

        foreach (array_chunk($rows, 1000) as $chunk) {
            DB::table('vendas')->insert($chunk);
        }

        $this->command->info('VendaSeeder concluída: ' . count($rows) . ' vendas inseridas.');
    }
}
