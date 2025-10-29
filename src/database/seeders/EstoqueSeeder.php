<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

// Ajuste os namespaces se necessário:
use App\Models\Produto;     // table: produtos,   PK: id_produto
use App\Models\Fornecedor;  // table: fornecedores, PK: id_fornecedor
use App\Models\Marca;       // table: marcas,    PK: id_marca

class EstoqueSeeder extends Seeder
{
    public function run(): void
    {
        $produtoIds    = Produto::query()->pluck('id_produto')->all();
        $fornecedorIds = Fornecedor::query()->pluck('id_fornecedor')->all();
        $marcaIds      = Marca::query()->pluck('id_marca')->all();

        if (empty($produtoIds) || empty($fornecedorIds) || empty($marcaIds)) {
            $this->command->warn('Produtos/Fornecedores/Marcas não encontrados. Rode as seeds deles antes da EstoqueSeeder.');
            return;
        }

        $now  = Carbon::now();
        $rows = [];

        // Para cada produto, criar 1 estoque para CADA fornecedor (80 x 31 = 2480 registros)
        foreach ($produtoIds as $idProduto) {
            foreach ($fornecedorIds as $idFornecedor) {
                $idMarca = $marcaIds[array_rand($marcaIds)];

                $quantidade   = rand(5, 120);
                $precoCusto   = round(mt_rand(100, 10000) / 100, 2); // 1,00 a 100,00
                $markupList   = [1.12, 1.18, 1.25, 1.35, 1.50];
                $markup       = $markupList[array_rand($markupList)];
                $precoVenda   = round($precoCusto * $markup, 2);

                $aliquotaList = [0.00, 0.12, 0.18];
                $aliquota     = $aliquotaList[array_rand($aliquotaList)];
                $impostoTotal = round($precoVenda * $aliquota * $quantidade, 2);

                // Lote único por (produto, fornecedor, lote)
                // Geramos e, por segurança, verificamos colisão
                $lote = strtoupper(Str::random(10));
                while (DB::table('estoques')->where([
                    'id_produto_fk'    => $idProduto,
                    'id_fornecedor_fk' => $idFornecedor,
                    'lote'             => $lote,
                ])->exists()) {
                    $lote = strtoupper(Str::random(10));
                }

                $rows[] = [
                    'quantidade'        => $quantidade,
                    'preco_custo'       => $precoCusto,
                    'preco_venda'       => $precoVenda,
                    'lote'              => $lote,
                    'validade'          => Carbon::now()->addDays(rand(30, 720))->format('Y-m-d'),
                    'data_chegada'      => Carbon::now()->subDays(rand(0, 30))->format('Y-m-d'),
                    'localizacao'       => 'R' . rand(1, 5) . '-C' . rand(1, 20),
                    'quantidade_aviso'  => rand(2, 20),

                    'id_produto_fk'     => $idProduto,
                    'id_fornecedor_fk'  => $idFornecedor,
                    'id_marca_fk'       => $idMarca,

                    'id_users_fk'       => 1, // ajuste conforme sua base
                    'status'            => 1,

                    'imposto_total'     => $impostoTotal,
                    'impostos_json'     => json_encode([
                        'aliquota'  => $aliquota,
                        'base'      => 'preco_venda',
                        'descricao' => $aliquota == 0 ? 'Isento' : 'Tributado',
                    ], JSON_UNESCAPED_UNICODE),

                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
        }

        // upsert garantindo unicidade por (produto, fornecedor, lote)
        foreach (array_chunk($rows, 1000) as $chunk) {
            DB::table('estoques')->upsert(
                $chunk,
                ['id_produto_fk', 'id_fornecedor_fk', 'lote'], // uniqueBy
                // Campos a atualizar caso já exista (seu caso usualmente não atualiza)
                [
                    'quantidade', 'preco_custo', 'preco_venda', 'validade', 'data_chegada',
                    'localizacao', 'quantidade_aviso', 'id_marca_fk', 'id_users_fk',
                    'status', 'imposto_total', 'impostos_json', 'updated_at'
                ]
            );
        }

        $this->command->info('EstoqueSeeder concluída: ' . count($rows) . ' registros (um por fornecedor para cada produto).');
    }
}
