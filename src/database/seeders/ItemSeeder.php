<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Item;
use App\Models\UnidadeMedida;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = Categoria::query()->pluck('id_categoria')->all();
        $unidades = UnidadeMedida::query()->get(['id', 'codigo'])->keyBy('codigo');

        if (empty($categorias)) {
            $this->command?->warn('Categorias não encontradas. Rode CategoriaSeeder antes de ItemSeeder.');
            return;
        }

        $itens = [
            ['sku' => 'ITEM-0001', 'nome' => 'Arroz Branco 5kg', 'tipo' => 'produto', 'unidade' => 'KG', 'custo' => 12.50, 'preco_base' => 19.90, 'controla_estoque' => true],
            ['sku' => 'ITEM-0002', 'nome' => 'Feijão Carioca 1kg', 'tipo' => 'produto', 'unidade' => 'KG', 'custo' => 6.20, 'preco_base' => 9.90, 'controla_estoque' => true],
            ['sku' => 'ITEM-0003', 'nome' => 'Açúcar Refinado 1kg', 'tipo' => 'produto', 'unidade' => 'KG', 'custo' => 3.90, 'preco_base' => 5.90, 'controla_estoque' => true],
            ['sku' => 'ITEM-0004', 'nome' => 'Óleo de Soja 900ml', 'tipo' => 'produto', 'unidade' => 'ML', 'custo' => 5.10, 'preco_base' => 7.90, 'controla_estoque' => true],
            ['sku' => 'ITEM-0005', 'nome' => 'Leite Integral 1L', 'tipo' => 'produto', 'unidade' => 'L', 'custo' => 2.80, 'preco_base' => 4.50, 'controla_estoque' => true],
            ['sku' => 'ITEM-0006', 'nome' => 'Água Mineral 500ml', 'tipo' => 'produto', 'unidade' => 'ML', 'custo' => 0.60, 'preco_base' => 1.50, 'controla_estoque' => true],
            ['sku' => 'ITEM-0007', 'nome' => 'Sabão em Pó 1kg', 'tipo' => 'produto', 'unidade' => 'KG', 'custo' => 8.00, 'preco_base' => 12.90, 'controla_estoque' => true],
            ['sku' => 'ITEM-0008', 'nome' => 'Papel Higiênico (12 un)', 'tipo' => 'produto', 'unidade' => 'PCT', 'custo' => 9.50, 'preco_base' => 16.90, 'controla_estoque' => true],
            ['sku' => 'ITEM-0009', 'nome' => 'Refrigerante 2L', 'tipo' => 'produto', 'unidade' => 'L', 'custo' => 4.50, 'preco_base' => 7.50, 'controla_estoque' => true],
            ['sku' => 'ITEM-0010', 'nome' => 'Biscoito 200g', 'tipo' => 'produto', 'unidade' => 'G', 'custo' => 1.80, 'preco_base' => 3.90, 'controla_estoque' => true],
            ['sku' => 'ITEM-0011', 'nome' => 'Serviço de Entrega', 'tipo' => 'servico', 'unidade' => 'UN', 'custo' => 0.00, 'preco_base' => 15.00, 'controla_estoque' => false],
            ['sku' => 'ITEM-0012', 'nome' => 'Serviço de Montagem', 'tipo' => 'servico', 'unidade' => 'H', 'custo' => 0.00, 'preco_base' => 80.00, 'controla_estoque' => false],
        ];

        foreach ($itens as $idx => $item) {
            $categoriaId = $categorias[$idx % count($categorias)];
            $unidade = $unidades[$item['unidade']] ?? $unidades->first();

            Item::updateOrCreate(
                ['sku' => $item['sku']],
                [
                    'nome' => $item['nome'],
                    'tipo' => $item['tipo'],
                    'categoria_id' => $categoriaId,
                    'unidade_medida_id' => $unidade?->id,
                    'descricao' => $item['nome'],
                    'custo' => $item['custo'],
                    'preco_base' => $item['preco_base'],
                    'peso_kg' => $item['tipo'] === 'produto' && $item['unidade'] === 'KG' ? 1 : null,
                    'volume_m3' => null,
                    'controla_estoque' => $item['controla_estoque'],
                    'ativo' => true,
                ]
            );
        }
    }
}
