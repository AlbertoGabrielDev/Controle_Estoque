<?php

namespace Modules\Products\Database\Seeders;

use App\Models\Categoria;
use App\Models\UnidadeMedida;
use App\Models\Item;
use Illuminate\Database\Seeder;
use Modules\Products\Database\Factories\ProdutoFactory;
use Modules\Products\Models\Produto;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $base  = ProdutoFactory::baseList();   // ← reaproveitando a lista da factory
        $total = 80;
        $unidades = UnidadeMedida::query()
            ->select(['id', 'codigo'])
            ->orderBy('codigo')
            ->get()
            ->values();
        $itens = Item::query()
            ->select(['id'])
            ->orderBy('id')
            ->get()
            ->values();

        for ($i = 1; $i <= $total; $i++) {
            $p   = $base[($i - 1) % count($base)];
            $cod = sprintf('PROD-%04d', $i);
            $unidade = $unidades->isNotEmpty()
                ? $unidades[$i % $unidades->count()]
                : null;
            $unidadeCodigo = $unidade?->codigo ?? ['KG', 'G', 'ML', 'L'][($i - 1) % 4];
            $item = $itens->isNotEmpty()
                ? $itens[$i % $itens->count()]
                : null;

            $produto = Produto::firstOrCreate(
                ['cod_produto' => $cod],
                [
                    'nome_produto'   => $p['nome'],
                    'descricao'      => mb_substr($p['descricao'], 0, 50),
                    'inf_nutriente'  => ProdutoFactory::normalizeNutritionList($p['inf_nutriente']),
                    'unidade_medida' => $unidadeCodigo,
                    'unidade_medida_id' => $unidade?->id,
                    'item_id'        => $item?->id,
                    'status'         => 1,
                    'id_users_fk'    => 1,
                ]
            );

            // categoriza sem duplicar vínculos
            $cats = Categoria::inRandomOrder()->take(rand(1, 3))->pluck('id_categoria');
            if ($cats->isNotEmpty()) {
                $produto->categorias()->syncWithoutDetaching($cats->all());
            }

            $this->command?->info(
                $produto->wasRecentlyCreated
                    ? "Criado: {$cod} - {$p['nome']}"
                    : "Já existia: {$cod} (não alterado)"
            );
        }
    }
}
