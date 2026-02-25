<?php

namespace Tests\Feature\Modules;

use Illuminate\Http\Request;
use Modules\Customers\Http\Controllers\ClienteController as ModuleClienteController;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class CustomersModulePhaseFourSmokeTest extends TestCase
{
    public function test_customers_routes_point_to_module_controller(): void
    {
        $route = app('router')->getRoutes()->getByName('clientes.index');

        $this->assertNotNull($route);
        $this->assertSame(ModuleClienteController::class . '@index', $route->getActionName());
        $this->assertStringContainsString('/verdurao/clientes/clientes', route('clientes.index'));
    }

    public function test_customers_module_controller_keeps_inertia_contract(): void
    {
        $controller = app(ModuleClienteController::class);
        $request = Request::create('/verdurao/clientes/clientes', 'GET', [
            'q' => 'joao',
            'uf' => 'SP',
            'segment_id' => '2',
            'ativo' => '1',
        ]);
        $request->headers->set('X-Inertia', 'true');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->headers->set('Accept', 'application/json');

        $payload = $controller->index($request)->toResponse($request)->getData(true);

        $this->assertSame('Clients/Index', $payload['component']);
        $this->assertSame('joao', $payload['props']['filters']['q'] ?? null);
        $this->assertSame('SP', $payload['props']['filters']['uf'] ?? null);
        $this->assertSame('2', $payload['props']['filters']['segment_id'] ?? null);
        $this->assertSame('1', $payload['props']['filters']['ativo'] ?? null);
    }

    public function test_customers_vue_pages_are_co_located_in_module_and_wrappers_exist(): void
    {
        $modulePages = [
            base_path('Modules/Customers/Resources/js/Pages/Clients/Index.vue'),
            base_path('Modules/Customers/Resources/js/Pages/Clients/Create.vue'),
            base_path('Modules/Customers/Resources/js/Pages/Clients/Edit.vue'),
            base_path('Modules/Customers/Resources/js/Pages/Clients/Show.vue'),
            base_path('Modules/Customers/Resources/js/Pages/Clients/ClienteForm.vue'),
        ];

        foreach ($modulePages as $path) {
            $this->assertFileExists($path);
        }

        $wrapperPath = base_path('resources/js/Pages/Clients/Index.vue');
        $this->assertFileExists($wrapperPath);
        $this->assertStringContainsString('@modules/Customers/Resources/js/Pages/Clients/Index.vue', (string) file_get_contents($wrapperPath));
    }

    public function test_customers_database_artifacts_are_co_located_in_module(): void
    {
        $expectedPaths = [
            base_path('Modules/Customers/Database/Migrations/2025_09_29_173148_create_clientes.php'),
            base_path('Modules/Customers/Database/Migrations/2025_09_29_174052_add_fk_segment_to_clients_table.php'),
            base_path('Modules/Customers/Database/Migrations/2025_09_30_201605_create_cliente_repositories_table.php'),
            base_path('Modules/Customers/Database/Migrations/2026_02_22_000008_add_mdm_fields_to_clientes.php'),
            base_path('Modules/Customers/Database/Seeders/ClientesSeeder.php'),
            base_path('Modules/Customers/Database/Factories'),
        ];

        foreach ($expectedPaths as $path) {
            $this->assertTrue(is_dir($path) || is_file($path), "Expected Customers DB artifact path: {$path}");
        }

        $this->assertFileDoesNotExist(database_path('migrations/2025_09_29_173148_create_clientes.php'));
        $this->assertFileDoesNotExist(database_path('migrations/2025_09_29_174052_add_fk_segment_to_clients_table.php'));
        $this->assertFileDoesNotExist(database_path('migrations/2025_09_30_201605_create_cliente_repositories_table.php'));
        $this->assertFileDoesNotExist(database_path('migrations/2026_02_22_000008_add_mdm_fields_to_clientes.php'));
    }

    public function test_legacy_customer_classes_are_wrappers_for_module_classes(): void
    {
        $this->assertTrue(is_subclass_of(\App\Http\Controllers\ClienteController::class, ModuleClienteController::class));
        $this->assertTrue(is_subclass_of(\App\Models\Cliente::class, \Modules\Customers\Models\Cliente::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\ClienteStoreRequest::class, \Modules\Customers\Http\Requests\ClienteStoreRequest::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\ClienteUpdateRequest::class, \Modules\Customers\Http\Requests\ClienteUpdateRequest::class));
        $this->assertTrue(is_subclass_of(\App\Services\ClienteService::class, \Modules\Customers\Services\ClienteService::class));
        $this->assertTrue(is_subclass_of(\App\Repositories\ClienteRepository::class, \Modules\Customers\Repositories\ClienteRepository::class));
        $this->assertTrue(is_subclass_of(\App\Repositories\ClienteRepositoryEloquent::class, \Modules\Customers\Repositories\ClienteRepositoryEloquent::class));
        $this->assertTrue(is_subclass_of(\Database\Seeders\ClientesSeeder::class, \Modules\Customers\Database\Seeders\ClientesSeeder::class));
    }

    public function test_customers_repository_and_service_bindings_resolve_module_classes(): void
    {
        $legacyRepo = app(\App\Repositories\ClienteRepository::class);
        $moduleRepo = app(\Modules\Customers\Repositories\ClienteRepository::class);
        $service = app(\App\Services\ClienteService::class);

        $this->assertInstanceOf(\App\Repositories\ClienteRepositoryEloquent::class, $legacyRepo);
        $this->assertInstanceOf(\Modules\Customers\Repositories\ClienteRepositoryEloquent::class, $legacyRepo);
        $this->assertInstanceOf(\Modules\Customers\Repositories\ClienteRepository::class, $legacyRepo);

        $this->assertInstanceOf(\Modules\Customers\Repositories\ClienteRepositoryEloquent::class, $moduleRepo);
        $this->assertInstanceOf(\Modules\Customers\Repositories\ClienteRepository::class, $moduleRepo);
        $this->assertInstanceOf(\Modules\Customers\Services\ClienteService::class, $service);
    }
}
