<?php

namespace Tests\Feature\Modules;

use Illuminate\Http\Request;
use Modules\Brands\Http\Controllers\MarcaController as ModuleMarcaController;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class BrandsModulePhaseFourSmokeTest extends TestCase
{
    public function test_brands_routes_point_to_module_controller(): void
    {
        $route = app('router')->getRoutes()->getByName('marca.index');

        $this->assertNotNull($route);
        $this->assertSame(ModuleMarcaController::class . '@index', $route->getActionName());
        $this->assertStringContainsString('/verdurao/marca/index', route('marca.index'));
    }

    public function test_brands_module_controller_keeps_inertia_contract(): void
    {
        $controller = app(ModuleMarcaController::class);
        $request = Request::create('/verdurao/marca/index', 'GET', ['q' => 'acme', 'status' => '1']);
        $request->headers->set('X-Inertia', 'true');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->headers->set('Accept', 'application/json');

        $payload = $controller->index($request)->toResponse($request)->getData(true);

        $this->assertSame('Brands/Index', $payload['component']);
        $this->assertSame('acme', $payload['props']['filters']['q'] ?? null);
        $this->assertSame('1', $payload['props']['filters']['status'] ?? null);
    }

    public function test_brands_vue_pages_are_co_located_in_module_and_wrappers_exist(): void
    {
        $modulePages = [
            base_path('Modules/Brands/Resources/js/Pages/Brands/Index.vue'),
            base_path('Modules/Brands/Resources/js/Pages/Brands/Create.vue'),
            base_path('Modules/Brands/Resources/js/Pages/Brands/Edit.vue'),
            base_path('Modules/Brands/Resources/js/Pages/Brands/BrandForm.vue'),
        ];

        foreach ($modulePages as $path) {
            $this->assertFileExists($path);
        }

        $wrapperPath = base_path('resources/js/Pages/Brands/Index.vue');
        $this->assertFileExists($wrapperPath);
        $this->assertStringContainsString('@modules/Brands/Resources/js/Pages/Brands/Index.vue', (string) file_get_contents($wrapperPath));
    }

    public function test_brands_database_artifacts_are_co_located_in_module(): void
    {
        $expectedPaths = [
            base_path('Modules/Brands/Database/Migrations/2023_10_06_005629_create_marca.php'),
            base_path('Modules/Brands/Database/Migrations/2023_10_06_010557_create_marca_produto.php'),
            base_path('Modules/Brands/Database/Seeders/MarcaSeeder.php'),
            base_path('Modules/Brands/Database/Factories/MarcaFactory.php'),
        ];

        foreach ($expectedPaths as $path) {
            $this->assertFileExists($path);
        }

        $this->assertFileDoesNotExist(database_path('migrations/2023_10_06_005629_create_marca.php'));
        $this->assertFileDoesNotExist(database_path('migrations/2023_10_06_010557_create_marca_produto.php'));
    }

    public function test_legacy_brand_classes_are_wrappers_for_module_classes(): void
    {
        $this->assertTrue(is_subclass_of(\App\Http\Controllers\MarcaController::class, ModuleMarcaController::class));
        $this->assertTrue(is_subclass_of(\App\Models\Marca::class, \Modules\Brands\Models\Marca::class));
        $this->assertTrue(is_subclass_of(\App\Services\MarcaService::class, \Modules\Brands\Services\MarcaService::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\ValidacaoMarca::class, \Modules\Brands\Http\Requests\ValidacaoMarca::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\ValidacaoMarcaEditar::class, \Modules\Brands\Http\Requests\ValidacaoMarcaEditar::class));
        $this->assertTrue(is_subclass_of(\Database\Seeders\MarcaSeeder::class, \Modules\Brands\Database\Seeders\MarcaSeeder::class));
        $this->assertTrue(is_subclass_of(\Database\Factories\MarcaFactory::class, \Modules\Brands\Database\Factories\MarcaFactory::class));
    }
}
