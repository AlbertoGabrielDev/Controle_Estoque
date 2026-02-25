<?php

namespace Tests\Feature\Modules;

use Illuminate\Http\Request;
use Modules\Stock\Http\Controllers\EstoqueController as ModuleEstoqueController;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class StockModulePhaseThreeSmokeTest extends TestCase
{
    public function test_stock_routes_point_to_module_controller(): void
    {
        $route = app('router')->getRoutes()->getByName('estoque.index');

        $this->assertNotNull($route);
        $this->assertSame(ModuleEstoqueController::class . '@index', $route->getActionName());
        $this->assertStringContainsString('/verdurao/estoque', route('estoque.index'));
    }

    public function test_stock_module_controller_keeps_inertia_contract(): void
    {
        $controller = app(ModuleEstoqueController::class);
        $request = Request::create('/verdurao/estoque', 'GET', ['q' => 'uva', 'status' => '1']);
        $request->headers->set('X-Inertia', 'true');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->headers->set('Accept', 'application/json');

        $payload = $controller->index($request)->toResponse($request)->getData(true);

        $this->assertSame('Stock/Index', $payload['component']);
        $this->assertSame('uva', $payload['props']['filters']['q'] ?? null);
        $this->assertSame('1', $payload['props']['filters']['status'] ?? null);
    }

    public function test_stock_vue_pages_are_co_located_in_module(): void
    {
        $expectedPages = [
            base_path('Modules/Stock/Resources/js/Pages/Stock/Index.vue'),
            base_path('Modules/Stock/Resources/js/Pages/Stock/Create.vue'),
            base_path('Modules/Stock/Resources/js/Pages/Stock/Edit.vue'),
            base_path('Modules/Stock/Resources/js/Pages/Stock/History.vue'),
            base_path('Modules/Stock/Resources/js/Pages/Stock/StockForm.vue'),
            base_path('Modules/Stock/Resources/js/Pages/Stock/StockTaxPreview.vue'),
        ];

        foreach ($expectedPages as $pagePath) {
            $this->assertFileExists($pagePath);
        }
    }

    public function test_stock_database_artifacts_are_co_located_in_module(): void
    {
        $expectedPaths = [
            base_path('Modules/Stock/Database/Migrations/2023_10_06_010906_create_estoque.php'),
            base_path('Modules/Stock/Database/Migrations/2024_05_17_155538_table_historico_add_column_venda.php'),
            base_path('Modules/Stock/Database/Migrations/2024_06_01_031310_unidade_estoque.php'),
            base_path('Modules/Stock/Database/Migrations/2025_10_12_201155_add_estoques_column_tax.php'),
            base_path('Modules/Stock/Database/Migrations/2026_02_23_120001_add_qrcode_to_estoques.php'),
            base_path('Modules/Stock/Database/Seeders/EstoqueSeeder.php'),
            base_path('Modules/Stock/Database/Factories'),
        ];

        foreach ($expectedPaths as $path) {
            $this->assertTrue(is_dir($path) || is_file($path), "Expected Stock DB artifact path: {$path}");
        }

        $this->assertFileDoesNotExist(base_path('database/migrations/2023_10_06_010906_create_estoque.php'));
        $this->assertFileDoesNotExist(base_path('database/migrations/2026_02_23_120001_add_qrcode_to_estoques.php'));
    }

    public function test_legacy_stock_classes_are_wrappers_for_module_classes(): void
    {
        $this->assertTrue(is_subclass_of(\App\Http\Controllers\EstoqueController::class, ModuleEstoqueController::class));
        $this->assertTrue(is_subclass_of(\App\Services\EstoqueService::class, \Modules\Stock\Services\EstoqueService::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\ValidacaoEstoque::class, \Modules\Stock\Http\Requests\ValidacaoEstoque::class));
        $this->assertTrue(is_subclass_of(\App\Models\Estoque::class, \Modules\Stock\Models\Estoque::class));
        $this->assertTrue(is_subclass_of(\App\Repositories\EstoqueRepository::class, \Modules\Stock\Repositories\EstoqueRepository::class));
        $this->assertTrue(is_subclass_of(\App\Repositories\EstoqueRepositoryEloquent::class, \Modules\Stock\Repositories\EstoqueRepositoryEloquent::class));
    }

    public function test_stock_repository_binding_resolves_module_implementation(): void
    {
        $repo = app(\App\Repositories\EstoqueRepository::class);

        $this->assertInstanceOf(\App\Repositories\EstoqueRepositoryEloquent::class, $repo);
        $this->assertInstanceOf(\Modules\Stock\Repositories\EstoqueRepositoryEloquent::class, $repo);
        $this->assertInstanceOf(\Modules\Stock\Repositories\EstoqueRepository::class, $repo);
        $this->assertSame(\Modules\Stock\Models\Estoque::class, $repo->model());
    }
}
