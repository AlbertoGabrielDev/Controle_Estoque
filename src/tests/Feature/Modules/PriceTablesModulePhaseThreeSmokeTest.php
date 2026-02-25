<?php

namespace Tests\Feature\Modules;

use Illuminate\Http\Request;
use Modules\PriceTables\Http\Controllers\TabelaPrecoController as ModuleTabelaPrecoController;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class PriceTablesModulePhaseThreeSmokeTest extends TestCase
{
    public function test_price_tables_routes_point_to_module_controller(): void
    {
        $route = app('router')->getRoutes()->getByName('tabelas_preco.index');

        $this->assertNotNull($route);
        $this->assertSame(ModuleTabelaPrecoController::class . '@index', $route->getActionName());
        $this->assertStringContainsString('/verdurao/cadastros/tabelas-preco', route('tabelas_preco.index'));
    }

    public function test_price_tables_module_controller_keeps_inertia_contract(): void
    {
        $controller = app(ModuleTabelaPrecoController::class);
        $request = Request::create('/verdurao/cadastros/tabelas-preco', 'GET', ['q' => 'promo', 'ativo' => '1']);
        $request->headers->set('X-Inertia', 'true');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->headers->set('Accept', 'application/json');

        $payload = $controller->index($request)->toResponse($request)->getData(true);

        $this->assertSame('PriceTables/Index', $payload['component']);
        $this->assertSame('promo', $payload['props']['filters']['q'] ?? null);
        $this->assertSame('1', $payload['props']['filters']['ativo'] ?? null);
    }

    public function test_price_tables_vue_pages_are_co_located_in_module(): void
    {
        $expectedPages = [
            base_path('Modules/PriceTables/Resources/js/Pages/PriceTables/Index.vue'),
            base_path('Modules/PriceTables/Resources/js/Pages/PriceTables/Create.vue'),
            base_path('Modules/PriceTables/Resources/js/Pages/PriceTables/Edit.vue'),
            base_path('Modules/PriceTables/Resources/js/Pages/PriceTables/Form.vue'),
        ];

        foreach ($expectedPages as $pagePath) {
            $this->assertFileExists($pagePath);
        }
    }

    public function test_price_tables_database_artifacts_are_co_located_in_module(): void
    {
        $expectedPaths = [
            base_path('Modules/PriceTables/Database/Migrations/2026_02_22_000003_create_tabelas_preco_table.php'),
            base_path('Modules/PriceTables/Database/Migrations/2026_02_22_000004_create_tabela_preco_itens_table.php'),
            base_path('Modules/PriceTables/Database/Seeders/TabelaPrecoSeeder.php'),
            base_path('Modules/PriceTables/Database/Factories'),
        ];

        foreach ($expectedPaths as $path) {
            $this->assertTrue(is_dir($path) || is_file($path), "Expected PriceTables DB artifact path: {$path}");
        }

        $this->assertFileDoesNotExist(base_path('database/migrations/2026_02_22_000003_create_tabelas_preco_table.php'));
        $this->assertFileDoesNotExist(base_path('database/migrations/2026_02_22_000004_create_tabela_preco_itens_table.php'));
    }

    public function test_legacy_price_tables_classes_are_wrappers_for_module_classes(): void
    {
        $this->assertTrue(is_subclass_of(\App\Http\Controllers\TabelaPrecoController::class, ModuleTabelaPrecoController::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\TabelaPrecoStoreRequest::class, \Modules\PriceTables\Http\Requests\TabelaPrecoStoreRequest::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\TabelaPrecoUpdateRequest::class, \Modules\PriceTables\Http\Requests\TabelaPrecoUpdateRequest::class));
        $this->assertTrue(is_subclass_of(\App\Services\TabelaPrecoService::class, \Modules\PriceTables\Services\TabelaPrecoService::class));
        $this->assertTrue(is_subclass_of(\App\Repositories\TabelaPrecoRepository::class, \Modules\PriceTables\Repositories\TabelaPrecoRepository::class));
        $this->assertTrue(is_subclass_of(\App\Models\TabelaPreco::class, \Modules\PriceTables\Models\TabelaPreco::class));
    }

    public function test_price_tables_repository_and_service_bindings_resolve_module_classes(): void
    {
        $repo = app(\App\Repositories\TabelaPrecoRepository::class);
        $service = app(\App\Services\TabelaPrecoService::class);

        $this->assertInstanceOf(\App\Repositories\TabelaPrecoRepositoryEloquent::class, $repo);
        $this->assertInstanceOf(\Modules\PriceTables\Repositories\TabelaPrecoRepositoryEloquent::class, $repo);
        $this->assertInstanceOf(\Modules\PriceTables\Repositories\TabelaPrecoRepository::class, $repo);
        $this->assertInstanceOf(\Modules\PriceTables\Services\TabelaPrecoService::class, $service);
    }
}
