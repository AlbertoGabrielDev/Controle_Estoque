<?php

namespace Tests\Feature\Modules;

use Illuminate\Http\Request;
use Modules\Customers\Http\Controllers\CustomerSegmentController as ModuleCustomerSegmentController;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class CustomerSegmentsModulePhaseFourSmokeTest extends TestCase
{
    public function test_segments_routes_point_to_module_controller(): void
    {
        $route = app('router')->getRoutes()->getByName('segmentos.index');

        $this->assertNotNull($route);
        $this->assertSame(ModuleCustomerSegmentController::class . '@index', $route->getActionName());
        $this->assertStringContainsString('/verdurao/clientes/segmentos', route('segmentos.index'));
    }

    public function test_segments_module_controller_keeps_inertia_contract(): void
    {
        $controller = app(ModuleCustomerSegmentController::class);
        $request = Request::create('/verdurao/clientes/segmentos', 'GET', ['q' => 'varejo']);
        $request->headers->set('X-Inertia', 'true');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->headers->set('Accept', 'application/json');

        $payload = $controller->index($request)->toResponse($request)->getData(true);

        $this->assertSame('Segments/Index', $payload['component']);
        $this->assertSame('varejo', $payload['props']['filters']['q'] ?? null);
    }

    public function test_segments_vue_pages_are_co_located_in_module_and_wrappers_exist(): void
    {
        $modulePages = [
            base_path('Modules/Customers/Resources/js/Pages/Segments/Index.vue'),
            base_path('Modules/Customers/Resources/js/Pages/Segments/Create.vue'),
            base_path('Modules/Customers/Resources/js/Pages/Segments/Edit.vue'),
        ];

        foreach ($modulePages as $path) {
            $this->assertFileExists($path);
        }

        $wrapperPath = base_path('resources/js/Pages/Segments/Index.vue');
        $this->assertFileExists($wrapperPath);
        $this->assertStringContainsString('@modules/Customers/Resources/js/Pages/Segments/Index.vue', (string) file_get_contents($wrapperPath));
    }

    public function test_segments_database_artifacts_are_co_located_in_module(): void
    {
        $this->assertFileExists(base_path('Modules/Customers/Database/Migrations/2025_09_29_173214_create_customer_segments.php'));
        $this->assertFileDoesNotExist(database_path('migrations/2025_09_29_173214_create_customer_segments.php'));
    }

    public function test_legacy_segment_classes_are_wrappers_for_module_classes(): void
    {
        $this->assertTrue(is_subclass_of(\App\Http\Controllers\CustomerSegmentController::class, ModuleCustomerSegmentController::class));
        $this->assertTrue(is_subclass_of(\App\Models\CustomerSegment::class, \Modules\Customers\Models\CustomerSegment::class));
        $this->assertTrue(is_subclass_of(\App\Http\Requests\CustomerSegmentRequest::class, \Modules\Customers\Http\Requests\CustomerSegmentRequest::class));
        $this->assertTrue(is_subclass_of(\App\Services\CustomerSegmentService::class, \Modules\Customers\Services\CustomerSegmentService::class));
    }
}
