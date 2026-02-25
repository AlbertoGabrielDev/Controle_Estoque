<?php

namespace Tests\Feature\Modules;

use Illuminate\Http\Request;
use Modules\Units\Http\Controllers\UnidadeController as ModuleUnidadeController;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class UnitsModulePhaseFourSmokeTest extends TestCase
{
    public function test_units_routes_point_to_module_controller(): void
    {
        $route = app('router')->getRoutes()->getByName('unidade.index');

        $this->assertNotNull($route);
        $this->assertSame(ModuleUnidadeController::class . '@index', $route->getActionName());
        $this->assertStringContainsString('/verdurao/unidades/index', route('unidade.index'));
    }

    public function test_units_module_controller_keeps_inertia_contract(): void
    {
        $controller = app(ModuleUnidadeController::class);
        $request = Request::create('/verdurao/unidades/index', 'GET', ['q' => 'central', 'status' => '1']);
        $request->headers->set('X-Inertia', 'true');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->headers->set('Accept', 'application/json');

        $payload = $controller->index($request)->toResponse($request)->getData(true);

        $this->assertSame('Units/Index', $payload['component']);
        $this->assertSame('central', $payload['props']['filters']['q'] ?? null);
        $this->assertSame('1', $payload['props']['filters']['status'] ?? null);
    }

    public function test_units_vue_pages_are_co_located_in_module_and_wrappers_exist(): void
    {
        $modulePages = [
            base_path('Modules/Units/Resources/js/Pages/Units/Index.vue'),
            base_path('Modules/Units/Resources/js/Pages/Units/Create.vue'),
            base_path('Modules/Units/Resources/js/Pages/Units/Edit.vue'),
            base_path('Modules/Units/Resources/js/Pages/Units/UnitForm.vue'),
        ];

        foreach ($modulePages as $path) {
            $this->assertFileExists($path);
        }

        $wrapperPath = base_path('resources/js/Pages/Units/Index.vue');
        $this->assertFileExists($wrapperPath);
        $this->assertStringContainsString('@modules/Units/Resources/js/Pages/Units/Index.vue', (string) file_get_contents($wrapperPath));
    }

    public function test_units_database_artifacts_are_co_located_in_module(): void
    {
        $expectedPaths = [
            base_path('Modules/Units/Database/Migrations/2024_05_27_161001_create_unidades.php'),
            base_path('Modules/Units/Database/Migrations/2024_06_10_224924_create_table_id_unidade_em_historicos_table.php'),
            base_path('Modules/Units/Database/Migrations/2024_06_13_013810_table_id_unidade_fk_em_users.php'),
            base_path('Modules/Units/Database/Seeders/UnidadeSeeder.php'),
            base_path('Modules/Units/Database/Factories'),
        ];

        foreach ($expectedPaths as $path) {
            $this->assertTrue(is_dir($path) || is_file($path), "Expected Units DB artifact path: {$path}");
        }

        $this->assertFileDoesNotExist(database_path('migrations/2024_05_27_161001_create_unidades.php'));
        $this->assertFileDoesNotExist(database_path('migrations/2024_06_10_224924_create_table_id_unidade_em_historicos_table.php'));
        $this->assertFileDoesNotExist(database_path('migrations/2024_06_13_013810_table_id_unidade_fk_em_users.php'));
    }

    public function test_legacy_units_classes_are_wrappers_for_module_classes(): void
    {
        $this->assertTrue(is_subclass_of(\App\Http\Controllers\UnidadeController::class, ModuleUnidadeController::class));
        $this->assertTrue(is_subclass_of(\App\Models\Unidades::class, \Modules\Units\Models\Unidades::class));
        $this->assertTrue(is_subclass_of(\App\Services\UnidadeService::class, \Modules\Units\Services\UnidadeService::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\UnidadeStoreRequest::class, \Modules\Units\Http\Requests\UnidadeStoreRequest::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\UnidadeUpdateRequest::class, \Modules\Units\Http\Requests\UnidadeUpdateRequest::class));
        $this->assertTrue(is_subclass_of(\Database\Seeders\UnidadeSeeder::class, \Modules\Units\Database\Seeders\UnidadeSeeder::class));
    }
}
