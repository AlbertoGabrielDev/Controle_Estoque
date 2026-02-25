<?php

namespace Tests\Feature\Modules;

use Illuminate\Http\Request;
use Modules\Categories\Http\Controllers\CategoriaController as ModuleCategoriaController;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class CategoriesModulePhaseFourSmokeTest extends TestCase
{
    public function test_categories_routes_point_to_module_controller(): void
    {
        $indexRoute = app('router')->getRoutes()->getByName('categoria.index');
        $homeRoute = app('router')->getRoutes()->getByName('categoria.inicio');

        $this->assertNotNull($indexRoute);
        $this->assertNotNull($homeRoute);
        $this->assertSame(ModuleCategoriaController::class . '@index', $indexRoute->getActionName());
        $this->assertSame(ModuleCategoriaController::class . '@inicio', $homeRoute->getActionName());
        $this->assertStringContainsString('/verdurao/categoria/index', route('categoria.index'));
    }

    public function test_categories_module_controller_keeps_inertia_contract_for_index(): void
    {
        $controller = app(ModuleCategoriaController::class);
        $request = Request::create('/verdurao/categoria/index', 'GET', ['q' => 'hort', 'status' => '1']);
        $request->headers->set('X-Inertia', 'true');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->headers->set('Accept', 'application/json');

        $payload = $controller->index($request)->toResponse($request)->getData(true);

        $this->assertSame('Categories/Index', $payload['component']);
        $this->assertSame('hort', $payload['props']['filters']['q'] ?? null);
        $this->assertSame('1', $payload['props']['filters']['status'] ?? null);
    }

    public function test_categories_vue_pages_are_co_located_in_module_and_wrappers_exist(): void
    {
        $modulePages = [
            base_path('Modules/Categories/Resources/js/Pages/Categories/Home.vue'),
            base_path('Modules/Categories/Resources/js/Pages/Categories/Index.vue'),
            base_path('Modules/Categories/Resources/js/Pages/Categories/Create.vue'),
            base_path('Modules/Categories/Resources/js/Pages/Categories/Edit.vue'),
            base_path('Modules/Categories/Resources/js/Pages/Categories/Products.vue'),
            base_path('Modules/Categories/Resources/js/Pages/Categories/CategoryForm.vue'),
        ];

        foreach ($modulePages as $path) {
            $this->assertFileExists($path);
        }

        $wrapperPath = base_path('resources/js/Pages/Categories/Index.vue');
        $this->assertFileExists($wrapperPath);
        $this->assertStringContainsString('@modules/Categories/Resources/js/Pages/Categories/Index.vue', (string) file_get_contents($wrapperPath));
    }

    public function test_categories_database_artifacts_are_co_located_in_module(): void
    {
        $expectedPaths = [
            base_path('Modules/Categories/Database/Migrations/2023_10_06_005754_create_categoria.php'),
            base_path('Modules/Categories/Database/Migrations/2023_10_06_010439_create_categoria_produto.php'),
            base_path('Modules/Categories/Database/Migrations/2026_02_22_000010_add_mdm_fields_to_categorias.php'),
            base_path('Modules/Categories/Database/Seeders/CategoriaSeeder.php'),
            base_path('Modules/Categories/Database/Factories/CategoriaFactory.php'),
        ];

        foreach ($expectedPaths as $path) {
            $this->assertFileExists($path);
        }

        $this->assertFileDoesNotExist(database_path('migrations/2023_10_06_005754_create_categoria.php'));
        $this->assertFileDoesNotExist(database_path('migrations/2023_10_06_010439_create_categoria_produto.php'));
        $this->assertFileDoesNotExist(database_path('migrations/2026_02_22_000010_add_mdm_fields_to_categorias.php'));
    }

    public function test_legacy_category_classes_are_wrappers_for_module_classes(): void
    {
        $this->assertTrue(is_subclass_of(\App\Http\Controllers\CategoriaController::class, ModuleCategoriaController::class));
        $this->assertTrue(is_subclass_of(\App\Models\Categoria::class, \Modules\Categories\Models\Categoria::class));
        $this->assertTrue(is_subclass_of(\App\Services\CategoriaService::class, \Modules\Categories\Services\CategoriaService::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\CategoriaStoreRequest::class, \Modules\Categories\Http\Requests\CategoriaStoreRequest::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\CategoriaUpdateRequest::class, \Modules\Categories\Http\Requests\CategoriaUpdateRequest::class));
        $this->assertTrue(is_subclass_of(\Database\Seeders\CategoriaSeeder::class, \Modules\Categories\Database\Seeders\CategoriaSeeder::class));
        $this->assertTrue(is_subclass_of(\Database\Factories\CategoriaFactory::class, \Modules\Categories\Database\Factories\CategoriaFactory::class));
    }
}
