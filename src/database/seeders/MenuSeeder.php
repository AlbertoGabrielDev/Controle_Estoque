<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $inicio = Menu::updateOrCreate(
            ['slug' => 'inicio'],
            [
                'name' => 'Inicio',
                'slug' => 'inicio',
                'icon' => 'fas fa-home mr-2',
                'route' => 'categoria.inicio',
                'parent_id' => null,
                'order' => 1,
                // 'inertia' => false, // opcional
            ]
        );

        $produto = Menu::updateOrCreate(
            ['slug' => 'produtos'],
            [
                'name' => 'Produtos',
                'slug' => 'produtos',
                'icon' => 'fas fa-file-alt mr-2',
                'route' => 'produtos.index',
                'parent_id' => null,
                'order' => 2,
            ]
        );

        $fornecedor = Menu::updateOrCreate(
            ['slug' => 'fornecedores'],
            [
                'name' => 'Fornecedores',
                'slug' => 'fornecedores',
                'icon' => 'fas fa-address-book mr-2',
                'route' => 'fornecedor.index',
                'parent_id' => null,
                'order' => 3,
            ]
        );

        $marca = Menu::updateOrCreate(
            ['slug' => 'marca'],
            [
                'name' => 'Marca',
                'slug' => 'marca',
                'icon' => 'fas fa-hashtag mr-2',
                'route' => 'marca.index',
                'parent_id' => null,
                'order' => 4,
            ]
        );

        $estoques = Menu::updateOrCreate(
            ['name' => 'Estoques', 'parent_id' => null],
            [
                'name' => 'Estoques',
                'icon' => 'fas fa-server mr-2',
                'parent_id' => null,
                'order' => 5,
            ]
        );

        $usuarios = Menu::updateOrCreate(
            ['name' => 'Usuários', 'parent_id' => null],
            [
                'name' => 'Usuários',
                'icon' => 'fas fa-users mr-2',
                'parent_id' => null,
                'order' => 6,
            ]
        );

        $vendas = Menu::updateOrCreate(
            ['name' => 'Venda', 'parent_id' => null],
            [
                'name' => 'Venda',
                'icon' => 'fas fa-history mr-2',
                'parent_id' => null,
                'order' => 8,
            ]
        );

        $dasboard = Menu::updateOrCreate(
            ['slug' => 'dashboard'],
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'icon' => 'fas fa-history mr-2',
                'route' => 'dashboard.index',
                'parent_id' => null,
                'order' => 8,
            ]
        );

        $calendario = Menu::updateOrCreate(
            ['slug' => 'calendario'],
            [
                'name' => 'Calendario',
                'slug' => 'calendario',
                'icon' => 'fas fa-history mr-2',
                'route' => 'vendas.calendar',
                'parent_id' => null,
                'order' => 9,
            ]
        );

        $clientes = Menu::updateOrCreate(
            ['slug' => 'clientes'],
            [
                'name' => 'Clientes',
                'slug' => 'clientes',
                'icon' => 'fas fa-users mr-2',
                'parent_id' => null,
                'route' => 'clientes.index',
                'order' => 10,
            ]
        );

        $estoque = Menu::updateOrCreate(
            ['slug' => 'estoque'],
            [
                'name' => 'Estoque',
                'slug' => 'estoque',
                'icon' => 'fas fa-server mr-2',
                'route' => 'estoque.index',
                'parent_id' => $estoques->id,
                'order' => 1,
            ]
        );

        $historico = Menu::updateOrCreate(
            ['slug' => 'historico'],
            [
                'name' => 'Histórico',
                'slug' => 'historico',
                'icon' => 'fas fa-history mr-2',
                'route' => 'estoque.historico',
                'parent_id' => $estoques->id,
                'order' => 2,
            ]
        );

        $usuario = Menu::updateOrCreate(
            ['slug' => 'perfil'],
            [
                'name' => 'Perfil',
                'slug' => 'perfil',
                'icon' => 'fas fa-user-tie mr-2',
                'route' => 'usuario.index',
                'parent_id' => $usuarios->id,
                'order' => 1,
            ]
        );

        $unidade = Menu::updateOrCreate(
            ['slug' => 'unidade'],
            [
                'name' => 'Unidade',
                'slug' => 'unidade',
                'icon' => 'fa-solid fa-suitcase mr-2',
                'route' => 'unidade.index',
                'parent_id' => $usuarios->id,
                'order' => 2,
            ]
        );

        $roles = Menu::updateOrCreate(
            ['slug' => 'permissao'],
            [
                'name' => 'Permissão',
                'slug' => 'permissao',
                'icon' => 'fas fa-user-shield mr-2',
                'route' => 'roles.index',
                'parent_id' => $usuarios->id,
                'order' => 7,
            ]
        );

        $historicoVenda = Menu::updateOrCreate(
            ['slug' => 'vendas'],
            [
                'name' => 'Vendas',
                'slug' => 'vendas',
                'icon' => 'fas fa-money-bill-wave-alt mr-2',
                'route' => 'vendas.venda',
                'parent_id' => $vendas->id,
                'order' => 1,
            ]
        );

        $historicoVenda2 = Menu::updateOrCreate(
            ['slug' => 'historico_vendas'],
            [
                'name' => 'Histórico de Venda',
                'slug' => 'historico_vendas',
                'icon' => 'fas fa-history mr-2',
                'route' => 'vendas.historico_vendas',
                'parent_id' => $vendas->id,
                'order' => 2,
            ]
        );

        /**
         * =========================
         * NOVO: Configurações + Taxas
         * =========================
         */

        // Pai: Configurações
        $configuracoes = Menu::updateOrCreate(
            ['slug' => 'configuracoes'],
            [
                'name' => 'Configurações',
                'slug'  => 'configuracoes',
                'icon'  => 'fas fa-cog mr-2',
                'route' => null,           // pai sem rota
                'parent_id' => null,
                'order' => 11,
                // se quiser forçar Inertia no clique do pai (não recomendado sem rota):
                // 'inertia' => false,
            ]
        );

        // Filho: Taxas (Imposto) — ROTA Inertia: taxes.index
        $taxas = Menu::updateOrCreate(
            ['slug' => 'taxas'],
            [
                'name' => 'Taxas (Imposto)',
                'slug' => 'taxas',
                'icon' => 'fas fa-percent mr-2',
                'route' => 'taxes.index',  // defina essa rota no web.php
                'parent_id' => $configuracoes->id,
                'order' => 1,
                // força Link Inertia no seu Sidebar.vue
            ]
        );
    }
}
