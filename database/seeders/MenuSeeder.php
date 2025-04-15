<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $inicio = Menu::create([
            'name' => 'Inicio',
            'slug' => 'inicio',
            'icon' => 'fas fa-home mr-2',
            'route' => 'categoria.inicio',
            'parent_id' => null,
            'order' => 1,
        ]);
        $produto = Menu::create([
            'name' => 'Produtos',
            'slug' => 'produtos',
            'icon' => 'fas fa-file-alt mr-2',
            'route' => 'produtos.index',
            'parent_id' => null,
            'order' => 2,
        ]);

        $fornecedor = Menu::create([
            'name' => 'Fornecedores',
            'slug' => 'fornecedores',
            'icon' => 'fas fa-address-book mr-2',
            'route' => 'fornecedor.index',
            'parent_id' => null,
            'order' => 3,
        ]);
        $marca = Menu::create([
            'name' => 'Marca',
            'slug' => 'marca',
            'icon' => 'fas fa-hashtag mr-2',
            'route' => 'marca.index',
            'parent_id' => null,
            'order' => 4,
        ]);
        $estoques = Menu::create([
            'name' => 'Estoques',
            'icon' => 'fas fa-server mr-2',
            'order' => 5,
        ]);
        $estoque = Menu::create([
            'name' => 'Estoque',
            'slug' => 'estoque',
            'icon' => 'fas fa-server mr-2',
            'route' => 'estoque.index',
            'parent_id' => $estoques->id,
            'order' => 1,
        ]);
        $historico = Menu::create([
            'name' => 'Histórico',
            'slug' => 'historico',
            'icon' => 'fas fa-history mr-2',
            'route' => 'estoque.historico',
            'parent_id' => $estoques->id,
            'order' => 2,
        ]);

        $usuarios = Menu::create([
            'name' => 'Usuários',
            'icon' => 'fas fa-users mr-2',
            'order' => 6,
        ]);

        $usuario = Menu::create([
            'name' => 'Perfil',
            'slug' => 'perfil',
            'icon' => 'fas fa-user-tie mr-2',
            'route' => 'usuario.index',
            'parent_id' => $usuarios->id,
            'order' => 1,
        ]);

        $unidade = Menu::create([
            'name' => 'Unidade',
            'slug' => 'unidade',
            'icon' => 'fa-solid fa-suitcase',
            'route' => 'unidade.index',
            'parent_id' => $usuarios->id,
            'order' => 2,
        ]);

        $roles = Menu::create([
            'name' => 'Permissão',
            'slug' => 'permissao',
            'icon' => 'fas fa-user-shield mr-2',
            'route' => 'roles.index',
            'parent_id' => $usuarios->id,
            'order' => 7,
        ]);

        $vendas = Menu::create([
            'name' => 'Venda',
            'icon' => 'fas fa-history mr-2',
            'order' => 8,
        ]);

        $historicoVenda = Menu::create([
            'name' => 'Vendas',
            'slug' => 'vendas',
            'icon' => 'fas fa-money-bill-wave-alt',
            'route' => 'vendas.venda',
            'parent_id' => $vendas->id,
            'order' => 1,
        ]);

        $historicoVenda = Menu::create([
            'name' => 'Histórico de Venda',
            'slug' => 'historico_vendas',
            'icon' => 'fas fa-history mr-2',
            'route' => 'vendas.historico_vendas',
            'parent_id' => $vendas->id,
            'order' => 2,
        ]);
    }
}
