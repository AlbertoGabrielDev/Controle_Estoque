<?php

namespace Tests\Feature\Modules;

use Illuminate\Http\Request;
use Modules\Products\Http\Controllers\ProdutoController as ModuleProdutoController;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class ProductsModulePhaseTwoSmokeTest extends TestCase
{
    public function test_products_routes_point_to_module_controller(): void
    {
        $route = app('router')->getRoutes()->getByName('produtos.index');

        $this->assertNotNull($route);
        $this->assertSame(ModuleProdutoController::class . '@index', $route->getActionName());
        $this->assertStringContainsString('/verdurao/produtos/index', route('produtos.index'));
    }

    public function test_products_module_controller_keeps_inertia_contract(): void
    {
        $controller = app(ModuleProdutoController::class);
        $request = Request::create('/verdurao/produtos/index', 'GET', ['q' => 'banana', 'status' => '1']);
        $request->headers->set('X-Inertia', 'true');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->headers->set('Accept', 'application/json');

        $payload = $controller->index($request)->toResponse($request)->getData(true);

        $this->assertSame('Products/Index', $payload['component']);
        $this->assertSame('banana', $payload['props']['filters']['q'] ?? null);
        $this->assertSame('1', $payload['props']['filters']['status'] ?? null);
    }

    public function test_products_vue_pages_are_co_located_in_module(): void
    {
        $expectedPages = [
            base_path('Modules/Products/Resources/js/Pages/Products/Index.vue'),
            base_path('Modules/Products/Resources/js/Pages/Products/Create.vue'),
            base_path('Modules/Products/Resources/js/Pages/Products/Edit.vue'),
            base_path('Modules/Products/Resources/js/Pages/Products/ProductForm.vue'),
        ];

        foreach ($expectedPages as $pagePath) {
            $this->assertFileExists($pagePath);
        }
    }

    public function test_products_database_artifacts_are_co_located_in_module(): void
    {
        $expectedModuleFiles = [
            base_path('Modules/Products/Database/Migrations/2023_10_06_003747_create_produto.php'),
            base_path('Modules/Products/Database/Migrations/2026_02_23_000012_add_unidade_medida_id_to_produtos.php'),
            base_path('Modules/Products/Database/Migrations/2026_02_23_000013_expand_unidade_medida_columns.php'),
            base_path('Modules/Products/Database/Migrations/2026_02_23_000014_add_item_id_to_produtos.php'),
            base_path('Modules/Products/Database/Seeders/ProdutoSeeder.php'),
            base_path('Modules/Products/Database/Factories/ProdutoFactory.php'),
        ];

        foreach ($expectedModuleFiles as $path) {
            $this->assertFileExists($path);
        }

        $legacyMigrationFiles = [
            database_path('migrations/2023_10_06_003747_create_produto.php'),
            database_path('migrations/2026_02_23_000012_add_unidade_medida_id_to_produtos.php'),
            database_path('migrations/2026_02_23_000013_expand_unidade_medida_columns.php'),
            database_path('migrations/2026_02_23_000014_add_item_id_to_produtos.php'),
        ];

        foreach ($legacyMigrationFiles as $path) {
            $this->assertFileDoesNotExist($path);
        }
    }

    public function test_legacy_product_classes_are_wrappers_or_aliases_for_module_classes(): void
    {
        $this->assertTrue(is_subclass_of(\App\Http\Controllers\ProdutoController::class, ModuleProdutoController::class));
        $this->assertTrue(is_subclass_of(\App\Models\Produto::class, \Modules\Products\Models\Produto::class));
        $this->assertTrue(is_subclass_of(\App\Repositories\ProdutoRepository::class, \Modules\Products\Repositories\ProdutoRepository::class));
        $this->assertTrue(is_subclass_of(\Database\Seeders\ProdutoSeeder::class, \Modules\Products\Database\Seeders\ProdutoSeeder::class));
        $this->assertTrue(is_subclass_of(\Database\Factories\ProdutoFactory::class, \Modules\Products\Database\Factories\ProdutoFactory::class));
    }

    public function test_products_repository_binding_uses_module_model(): void
    {
        $repo = app(\App\Repositories\ProdutoRepository::class);

        $this->assertInstanceOf(\App\Repositories\ProdutoRepositoryEloquent::class, $repo);
        $this->assertInstanceOf(\Modules\Products\Repositories\ProdutoRepositoryEloquent::class, $repo);
        $this->assertInstanceOf(\Modules\Products\Repositories\ProdutoRepository::class, $repo);
        $this->assertInstanceOf(\Modules\Products\Models\Produto::class, $repo->modelInstance());
    }
}
