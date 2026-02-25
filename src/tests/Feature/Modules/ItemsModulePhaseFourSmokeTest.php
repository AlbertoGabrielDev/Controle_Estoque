<?php

namespace Tests\Feature\Modules;

use Illuminate\Http\Request;
use Modules\Items\Http\Controllers\ItemController as ModuleItemController;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class ItemsModulePhaseFourSmokeTest extends TestCase
{
    public function test_items_routes_point_to_module_controller(): void
    {
        $route = app('router')->getRoutes()->getByName('itens.index');

        $this->assertNotNull($route);
        $this->assertSame(ModuleItemController::class . '@index', $route->getActionName());
        $this->assertStringContainsString('/verdurao/cadastros/itens', route('itens.index'));
    }

    public function test_items_module_controller_keeps_inertia_contract(): void
    {
        $controller = app(ModuleItemController::class);
        $request = Request::create('/verdurao/cadastros/itens', 'GET', ['q' => 'ITEM', 'tipo' => 'produto', 'ativo' => '1']);
        $request->headers->set('X-Inertia', 'true');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->headers->set('Accept', 'application/json');

        $payload = $controller->index($request)->toResponse($request)->getData(true);

        $this->assertSame('Items/Index', $payload['component']);
        $this->assertSame('ITEM', $payload['props']['filters']['q'] ?? null);
        $this->assertSame('produto', $payload['props']['filters']['tipo'] ?? null);
        $this->assertSame('1', $payload['props']['filters']['ativo'] ?? null);
    }

    public function test_items_vue_pages_are_co_located_in_module_and_wrappers_exist(): void
    {
        $modulePages = [
            base_path('Modules/Items/Resources/js/Pages/Items/Index.vue'),
            base_path('Modules/Items/Resources/js/Pages/Items/Create.vue'),
            base_path('Modules/Items/Resources/js/Pages/Items/Edit.vue'),
            base_path('Modules/Items/Resources/js/Pages/Items/Form.vue'),
        ];

        foreach ($modulePages as $path) {
            $this->assertFileExists($path);
        }

        $wrapperPath = base_path('resources/js/Pages/Items/Index.vue');
        $this->assertFileExists($wrapperPath);
        $this->assertStringContainsString('@modules/Items/Resources/js/Pages/Items/Index.vue', (string) file_get_contents($wrapperPath));
    }

    public function test_items_database_artifacts_are_co_located_in_module(): void
    {
        $expectedPaths = [
            base_path('Modules/Items/Database/Migrations/2026_02_22_000002_create_itens_table.php'),
            base_path('Modules/Items/Database/Seeders/ItemSeeder.php'),
            base_path('Modules/Items/Database/Factories'),
        ];

        foreach ($expectedPaths as $path) {
            $this->assertTrue(is_dir($path) || is_file($path), "Expected Items DB artifact path: {$path}");
        }

        $this->assertFileDoesNotExist(database_path('migrations/2026_02_22_000002_create_itens_table.php'));
    }

    public function test_legacy_item_classes_are_wrappers_for_module_classes(): void
    {
        $this->assertTrue(is_subclass_of(\App\Http\Controllers\ItemController::class, ModuleItemController::class));
        $this->assertTrue(is_subclass_of(\App\Models\Item::class, \Modules\Items\Models\Item::class));
        $this->assertTrue(is_subclass_of(\App\Services\ItemService::class, \Modules\Items\Services\ItemService::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\ItemStoreRequest::class, \Modules\Items\Http\Requests\ItemStoreRequest::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\ItemUpdateRequest::class, \Modules\Items\Http\Requests\ItemUpdateRequest::class));
        $this->assertTrue(is_subclass_of(\Database\Seeders\ItemSeeder::class, \Modules\Items\Database\Seeders\ItemSeeder::class));
    }
}
