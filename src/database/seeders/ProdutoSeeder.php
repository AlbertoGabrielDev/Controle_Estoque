<?php

namespace Database\Seeders;

use App\Models\Produto;
use App\Models\Categoria;
use Database\Factories\ProdutoFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $base  = ProdutoFactory::baseList();   // ← reaproveitando a lista da factory
        $total = 80;

        for ($i = 1; $i <= $total; $i++) {
            $p   = $base[($i - 1) % count($base)];
            $cod = sprintf('PROD-%04d', $i);

            $produto = Produto::firstOrCreate(
                ['cod_produto' => $cod],
                [
                    'nome_produto'   => $p['nome'],
                    'descricao'      => mb_substr($p['descricao'], 0, 50),
                    'inf_nutriente'  => json_encode($p['inf_nutriente']),
                    'unidade_medida' => ['kg','g','ml','L'][($i - 1) % 4],
                    'status'         => 1,
                    'qrcode'         => (string) Str::uuid(),
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
