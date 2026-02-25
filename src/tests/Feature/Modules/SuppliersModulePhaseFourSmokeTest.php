<?php

namespace Tests\Feature\Modules;

use Illuminate\Http\Request;
use Modules\Suppliers\Http\Controllers\FornecedorController as ModuleFornecedorController;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class SuppliersModulePhaseFourSmokeTest extends TestCase
{
    public function test_suppliers_routes_point_to_module_controller(): void
    {
        $route = app('router')->getRoutes()->getByName('fornecedor.index');

        $this->assertNotNull($route);
        $this->assertSame(ModuleFornecedorController::class . '@index', $route->getActionName());
        $this->assertStringContainsString('/verdurao/fornecedor', route('fornecedor.index'));
    }

    public function test_suppliers_module_controller_keeps_inertia_contract(): void
    {
        $controller = app(ModuleFornecedorController::class);
        $request = Request::create('/verdurao/fornecedor', 'GET', ['q' => 'acme', 'status' => '1']);
        $request->headers->set('X-Inertia', 'true');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->headers->set('Accept', 'application/json');

        $payload = $controller->index($request)->toResponse($request)->getData(true);

        $this->assertSame('Suppliers/Index', $payload['component']);
        $this->assertSame('acme', $payload['props']['filters']['q'] ?? null);
        $this->assertSame('1', $payload['props']['filters']['status'] ?? null);
    }

    public function test_suppliers_vue_pages_are_co_located_in_module_and_wrappers_exist(): void
    {
        $modulePages = [
            base_path('Modules/Suppliers/Resources/js/Pages/Suppliers/Index.vue'),
            base_path('Modules/Suppliers/Resources/js/Pages/Suppliers/Create.vue'),
            base_path('Modules/Suppliers/Resources/js/Pages/Suppliers/Edit.vue'),
            base_path('Modules/Suppliers/Resources/js/Pages/Suppliers/SupplierForm.vue'),
        ];

        foreach ($modulePages as $path) {
            $this->assertFileExists($path);
        }

        $wrapperPath = base_path('resources/js/Pages/Suppliers/Index.vue');
        $this->assertFileExists($wrapperPath);
        $this->assertStringContainsString('@modules/Suppliers/Resources/js/Pages/Suppliers/Index.vue', (string) file_get_contents($wrapperPath));
    }

    public function test_suppliers_database_artifacts_are_co_located_in_module(): void
    {
        $expectedPaths = [
            base_path('Modules/Suppliers/Database/Migrations/2023_10_06_004538_create_fornecedor.php'),
            base_path('Modules/Suppliers/Database/Migrations/2026_02_22_000009_add_mdm_fields_to_fornecedores.php'),
            base_path('Modules/Suppliers/Database/Seeders/FornecedorSeeder.php'),
            base_path('Modules/Suppliers/Database/Factories/FornecedorFactory.php'),
        ];

        foreach ($expectedPaths as $path) {
            $this->assertFileExists($path);
        }

        $this->assertFileDoesNotExist(database_path('migrations/2023_10_06_004538_create_fornecedor.php'));
        $this->assertFileDoesNotExist(database_path('migrations/2026_02_22_000009_add_mdm_fields_to_fornecedores.php'));
    }

    public function test_legacy_supplier_classes_are_wrappers_for_module_classes(): void
    {
        $this->assertTrue(is_subclass_of(\App\Http\Controllers\FornecedorController::class, ModuleFornecedorController::class));
        $this->assertTrue(is_subclass_of(\App\Models\Fornecedor::class, \Modules\Suppliers\Models\Fornecedor::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\FornecedorStoreRequest::class, \Modules\Suppliers\Http\Requests\FornecedorStoreRequest::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\FornecedorUpdateRequest::class, \Modules\Suppliers\Http\Requests\FornecedorUpdateRequest::class));
        $this->assertTrue(is_subclass_of(\App\Services\FornecedorService::class, \Modules\Suppliers\Services\FornecedorService::class));
        $this->assertTrue(is_subclass_of(\Database\Seeders\FornecedorSeeder::class, \Modules\Suppliers\Database\Seeders\FornecedorSeeder::class));
        $this->assertTrue(is_subclass_of(\Database\Factories\FornecedorFactory::class, \Modules\Suppliers\Database\Factories\FornecedorFactory::class));
    }
}
