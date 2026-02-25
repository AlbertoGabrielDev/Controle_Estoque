<?php

namespace Tests\Feature\Modules;

use Inertia\Response as InertiaResponse;
use Modules\Sales\Http\Controllers\Api\CartController as ModuleApiCartController;
use Modules\Sales\Http\Controllers\Api\OrderController as ModuleApiOrderController;
use Modules\Sales\Http\Controllers\VendaController as ModuleVendaController;
use PHPUnit\Framework\Attributes\Group;
use ReflectionObject;
use Tests\TestCase;

#[Group('modularizacao')]
class SalesModulePhaseThreeSmokeTest extends TestCase
{
    public function test_sales_web_routes_point_to_module_controller(): void
    {
        $route = app('router')->getRoutes()->getByName('vendas.venda');

        $this->assertNotNull($route);
        $this->assertSame(ModuleVendaController::class . '@vendas', $route->getActionName());
        $this->assertStringContainsString('/verdurao/vendas', route('vendas.venda'));
    }

    public function test_sales_api_routes_point_to_module_api_controllers(): void
    {
        $upsert = $this->findRouteByMethodAndUri('POST', 'api/carts/upsert');
        $getByClient = $this->findRouteByMethodAndUri('GET', 'api/carts/by-client/{client}');
        $remove = $this->findRouteByMethodAndUri('POST', 'api/carts/remove');
        $orders = $this->findRouteByMethodAndUri('POST', 'api/orders');

        $this->assertNotNull($upsert);
        $this->assertSame(ModuleApiCartController::class . '@upsert', $upsert->getActionName());
        $this->assertNotNull($getByClient);
        $this->assertSame(ModuleApiCartController::class . '@getByclient', $getByClient->getActionName());
        $this->assertNotNull($remove);
        $this->assertSame(ModuleApiCartController::class . '@remove', $remove->getActionName());
        $this->assertNotNull($orders);
        $this->assertSame(ModuleApiOrderController::class . '@store', $orders->getActionName());
    }

    public function test_sales_module_controller_keeps_inertia_component_contract(): void
    {
        $response = app(ModuleVendaController::class)->vendas();

        $this->assertInstanceOf(InertiaResponse::class, $response);

        $reflection = new ReflectionObject($response);
        $component = $reflection->getProperty('component');
        $component->setAccessible(true);

        $this->assertSame('Sales/Index', $component->getValue($response));
    }

    public function test_sales_vue_pages_are_co_located_in_module(): void
    {
        $expectedPages = [
            base_path('Modules/Sales/Resources/js/Pages/Sales/Index.vue'),
            base_path('Modules/Sales/Resources/js/Pages/Sales/CartTable.vue'),
            base_path('Modules/Sales/Resources/js/Pages/Sales/ClientSelector.vue'),
            base_path('Modules/Sales/Resources/js/Pages/Sales/ManualCodeInput.vue'),
            base_path('Modules/Sales/Resources/js/Pages/Sales/QrScanner.vue'),
            base_path('Modules/Sales/Resources/js/Pages/Sales/RecentSalesTable.vue'),
        ];

        foreach ($expectedPages as $pagePath) {
            $this->assertFileExists($pagePath);
        }
    }

    public function test_sales_database_artifacts_are_co_located_in_module(): void
    {
        $expectedPaths = [
            base_path('Modules/Sales/Database/Migrations/2025_03_28_210806_create_vendas_table.php'),
            base_path('Modules/Sales/Database/Migrations/2025_08_14_210910_create_carts_table.php'),
            base_path('Modules/Sales/Database/Migrations/2025_08_14_210942_create_orders_table.php'),
            base_path('Modules/Sales/Database/Migrations/2026_02_23_120002_add_id_estoque_fk_to_cart_items.php'),
            base_path('Modules/Sales/Database/Seeders/VendaSeeder.php'),
            base_path('Modules/Sales/Database/Factories'),
        ];

        foreach ($expectedPaths as $path) {
            $this->assertTrue(is_dir($path) || is_file($path), "Expected Sales DB artifact path: {$path}");
        }

        $this->assertFileDoesNotExist(base_path('database/migrations/2025_03_28_210806_create_vendas_table.php'));
        $this->assertFileDoesNotExist(base_path('database/migrations/2025_08_14_210910_create_carts_table.php'));
        $this->assertFileDoesNotExist(base_path('database/migrations/2025_08_14_210942_create_orders_table.php'));
    }

    public function test_legacy_sales_classes_are_wrappers_for_module_classes(): void
    {
        $this->assertTrue(is_subclass_of(\App\Http\Controllers\VendaController::class, ModuleVendaController::class));
        $this->assertTrue(is_subclass_of(\App\Http\Controllers\Api\CartController::class, ModuleApiCartController::class));
        $this->assertTrue(is_subclass_of(\App\Http\Controllers\Api\OrderController::class, ModuleApiOrderController::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\CartUpsertRequest::class, \Modules\Sales\Http\Requests\CartUpsertRequest::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\OrderCreateRequest::class, \Modules\Sales\Http\Requests\OrderCreateRequest::class));
        $this->assertTrue(is_subclass_of(\App\Services\VendaService::class, \Modules\Sales\Services\VendaService::class));
        $this->assertTrue(is_subclass_of(\App\Models\Venda::class, \Modules\Sales\Models\Venda::class));
        $this->assertTrue(is_subclass_of(\App\Models\Cart::class, \Modules\Sales\Models\Cart::class));
        $this->assertTrue(is_subclass_of(\App\Models\CartItem::class, \Modules\Sales\Models\CartItem::class));
        $this->assertTrue(is_subclass_of(\App\Models\Order::class, \Modules\Sales\Models\Order::class));
        $this->assertTrue(is_subclass_of(\App\Models\OrderItem::class, \Modules\Sales\Models\OrderItem::class));
    }

    public function test_sales_repository_bindings_resolve_module_implementations(): void
    {
        $cartRepo = app(\Modules\Sales\Repositories\CartRepository::class);
        $orderRepo = app(\Modules\Sales\Repositories\OrderRepository::class);
        $vendaRepo = app(\Modules\Sales\Repositories\VendaRepository::class);

        $this->assertInstanceOf(\Modules\Sales\Repositories\CartRepositoryEloquent::class, $cartRepo);
        $this->assertInstanceOf(\Modules\Sales\Repositories\OrderRepositoryEloquent::class, $orderRepo);
        $this->assertInstanceOf(\Modules\Sales\Repositories\VendaRepositoryEloquent::class, $vendaRepo);
    }

    private function findRouteByMethodAndUri(string $method, string $uri): ?\Illuminate\Routing\Route
    {
        foreach (app('router')->getRoutes() as $route) {
            if ($route->uri() === $uri && in_array($method, $route->methods(), true)) {
                return $route;
            }
        }

        return null;
    }
}
