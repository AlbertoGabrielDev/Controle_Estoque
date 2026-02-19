<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('phase5')]
class Phase5CutoverContractsTest extends TestCase
{
    public function test_migrated_business_controllers_do_not_return_blade_views(): void
    {
        $controllers = [
            app_path('Http/Controllers/CategoriaController.php'),
            app_path('Http/Controllers/MarcaController.php'),
            app_path('Http/Controllers/UnidadeController.php'),
            app_path('Http/Controllers/ProdutoController.php'),
            app_path('Http/Controllers/EstoqueController.php'),
            app_path('Http/Controllers/VendaController.php'),
            app_path('Http/Controllers/SpreadsheetController.php'),
        ];

        foreach ($controllers as $controller) {
            $source = file_get_contents($controller);
            $this->assertStringNotContainsString('return view(', $source, "{$controller} ainda possui retorno Blade");
        }
    }

    public function test_obsolete_blades_from_migrated_modules_were_removed(): void
    {
        $obsoleteBlades = [
            resource_path('views/categorias/cadastro.blade.php'),
            resource_path('views/categorias/categoria.blade.php'),
            resource_path('views/categorias/editar.blade.php'),
            resource_path('views/categorias/index.blade.php'),
            resource_path('views/categorias/produto.blade.php'),
            resource_path('views/estoque/cadastro.blade.php'),
            resource_path('views/estoque/editar.blade.php'),
            resource_path('views/estoque/historico.blade.php'),
            resource_path('views/estoque/index.blade.php'),
            resource_path('views/marca/cadastro.blade.php'),
            resource_path('views/marca/editar.blade.php'),
            resource_path('views/marca/index.blade.php'),
            resource_path('views/produtos/cadastro.blade.php'),
            resource_path('views/produtos/editar.blade.php'),
            resource_path('views/produtos/index.blade.php'),
            resource_path('views/produtos/partials/acoes.blade.php'),
            resource_path('views/spreadsheets/index.blade.php'),
            resource_path('views/unidades/cadastro.blade.php'),
            resource_path('views/unidades/editar.blade.php'),
            resource_path('views/unidades/index.blade.php'),
            resource_path('views/vendas/venda.blade.php'),
            resource_path('views/vendas/historico_vendas.blade.php'),
            resource_path('views/vendas/qrcode.blade.php'),
        ];

        foreach ($obsoleteBlades as $bladePath) {
            $this->assertFileDoesNotExist($bladePath);
        }
    }

    public function test_inertia_menu_prefixes_cover_migrated_modules_contract(): void
    {
        $appJs = file_get_contents(resource_path('js/app.js'));
        $principalLayout = file_get_contents(resource_path('js/Layouts/PrincipalLayout.vue'));
        $middleware = file_get_contents(app_path('Http/Middleware/HandleInertiaRequests.php'));

        foreach (['categoria.', 'produtos.', 'estoque.', 'marca.', 'unidades.', 'vendas.', 'spreadsheet.'] as $prefix) {
            $this->assertStringContainsString($prefix, $appJs, "app.js sem prefixo {$prefix}");
            $this->assertStringContainsString($prefix, $principalLayout, "PrincipalLayout.vue sem prefixo {$prefix}");
        }

        $this->assertStringContainsString('resolveInertiaMenus', $middleware);
        $this->assertStringContainsString('hasPermission', $middleware);
    }
}
