<?php

namespace Tests\Feature\Modules;

use Illuminate\Http\Request;
use Modules\MeasureUnits\Http\Controllers\UnidadeMedidaController as ModuleUnidadeMedidaController;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class MeasureUnitsModulePhaseFourSmokeTest extends TestCase
{
    public function test_measure_units_routes_point_to_module_controller(): void
    {
        $route = app('router')->getRoutes()->getByName('unidades_medida.index');

        $this->assertNotNull($route);
        $this->assertSame(ModuleUnidadeMedidaController::class . '@index', $route->getActionName());
        $this->assertStringContainsString('/verdurao/cadastros/unidades-medida', route('unidades_medida.index'));
    }

    public function test_measure_units_module_controller_keeps_inertia_contract(): void
    {
        $controller = app(ModuleUnidadeMedidaController::class);
        $request = Request::create('/verdurao/cadastros/unidades-medida', 'GET', ['q' => 'KG', 'ativo' => '1']);
        $request->headers->set('X-Inertia', 'true');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->headers->set('Accept', 'application/json');

        $payload = $controller->index($request)->toResponse($request)->getData(true);

        $this->assertSame('MeasureUnits/Index', $payload['component']);
        $this->assertSame('KG', $payload['props']['filters']['q'] ?? null);
        $this->assertSame('1', $payload['props']['filters']['ativo'] ?? null);
    }

    public function test_measure_units_vue_pages_are_co_located_in_module_and_wrappers_exist(): void
    {
        $modulePages = [
            base_path('Modules/MeasureUnits/Resources/js/Pages/MeasureUnits/Index.vue'),
            base_path('Modules/MeasureUnits/Resources/js/Pages/MeasureUnits/Create.vue'),
            base_path('Modules/MeasureUnits/Resources/js/Pages/MeasureUnits/Edit.vue'),
            base_path('Modules/MeasureUnits/Resources/js/Pages/MeasureUnits/Form.vue'),
        ];

        foreach ($modulePages as $path) {
            $this->assertFileExists($path);
        }

        $wrapperPath = base_path('resources/js/Pages/MeasureUnits/Index.vue');
        $this->assertFileExists($wrapperPath);
        $this->assertStringContainsString('@modules/MeasureUnits/Resources/js/Pages/MeasureUnits/Index.vue', (string) file_get_contents($wrapperPath));
    }

    public function test_measure_units_database_artifacts_are_co_located_in_module(): void
    {
        $expectedPaths = [
            base_path('Modules/MeasureUnits/Database/Migrations/2026_02_22_000001_create_unidades_medida_table.php'),
            base_path('Modules/MeasureUnits/Database/Seeders/UnidadeMedidaSeeder.php'),
            base_path('Modules/MeasureUnits/Database/Factories'),
        ];

        foreach ($expectedPaths as $path) {
            $this->assertTrue(is_dir($path) || is_file($path), "Expected MeasureUnits DB artifact path: {$path}");
        }

        $this->assertFileDoesNotExist(database_path('migrations/2026_02_22_000001_create_unidades_medida_table.php'));
    }

    public function test_legacy_measure_units_classes_are_wrappers_for_module_classes(): void
    {
        $this->assertTrue(is_subclass_of(\App\Http\Controllers\UnidadeMedidaController::class, ModuleUnidadeMedidaController::class));
        $this->assertTrue(is_subclass_of(\App\Models\UnidadeMedida::class, \Modules\MeasureUnits\Models\UnidadeMedida::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\UnidadeMedidaRequest::class, \Modules\MeasureUnits\Http\Requests\UnidadeMedidaRequest::class));
        $this->assertTrue(is_subclass_of(\App\Services\UnidadeMedidaService::class, \Modules\MeasureUnits\Services\UnidadeMedidaService::class));
        $this->assertTrue(is_subclass_of(\Database\Seeders\UnidadeMedidaSeeder::class, \Modules\MeasureUnits\Database\Seeders\UnidadeMedidaSeeder::class));
    }
}
