<?php

namespace Tests\Feature;

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\UnidadeController;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('phase2')]
class Phase2InertiaComponentsTest extends TestCase
{
    public function test_brands_index_component_and_filters_contract(): void
    {
        $controller = app(MarcaController::class);
        $request = $this->makeInertiaRequest('/verdurao/marca/index', ['q' => 'acme', 'status' => '1']);
        $payload = $this->toInertiaPayload($controller->index($request), $request);

        $this->assertSame('Brands/Index', $payload['component']);
        $this->assertSame('acme', $payload['props']['filters']['q'] ?? null);
        $this->assertSame('1', $payload['props']['filters']['status'] ?? null);
    }

    public function test_brands_create_component_contract(): void
    {
        $controller = app(MarcaController::class);
        $request = $this->makeInertiaRequest('/verdurao/marca/cadastro');
        $payload = $this->toInertiaPayload($controller->cadastro(), $request);

        $this->assertSame('Brands/Create', $payload['component']);
    }

    public function test_units_index_component_and_filters_contract(): void
    {
        $controller = app(UnidadeController::class);
        $request = $this->makeInertiaRequest('/verdurao/unidades/index', ['q' => 'matriz', 'status' => '0']);
        $payload = $this->toInertiaPayload($controller->index($request), $request);

        $this->assertSame('Units/Index', $payload['component']);
        $this->assertSame('matriz', $payload['props']['filters']['q'] ?? null);
        $this->assertSame('0', $payload['props']['filters']['status'] ?? null);
    }

    public function test_units_create_component_contract(): void
    {
        $controller = app(UnidadeController::class);
        $request = $this->makeInertiaRequest('/verdurao/unidades/cadastro');
        $payload = $this->toInertiaPayload($controller->cadastro(), $request);

        $this->assertSame('Units/Create', $payload['component']);
    }

    public function test_categories_index_component_and_filters_contract(): void
    {
        $controller = app(CategoriaController::class);
        $request = $this->makeInertiaRequest('/verdurao/categoria/index', ['q' => 'frutas', 'status' => '1']);
        $payload = $this->toInertiaPayload($controller->index($request), $request);

        $this->assertSame('Categories/Index', $payload['component']);
        $this->assertSame('frutas', $payload['props']['filters']['q'] ?? null);
        $this->assertSame('1', $payload['props']['filters']['status'] ?? null);
    }

    public function test_categories_create_component_contract(): void
    {
        $controller = app(CategoriaController::class);
        $request = $this->makeInertiaRequest('/verdurao/categoria/cadastro');
        $payload = $this->toInertiaPayload($controller->cadastro(), $request);

        $this->assertSame('Categories/Create', $payload['component']);
    }

    public function test_phase_two_routes_expose_datatable_endpoints(): void
    {
        $this->assertStringContainsString('/verdurao/marca/data', route('marca.data'));
        $this->assertStringContainsString('/verdurao/unidades/data', route('unidade.data'));
        $this->assertStringContainsString('/verdurao/categoria/data', route('categoria.data'));
    }

    private function makeInertiaRequest(string $uri, array $query = []): Request
    {
        $request = Request::create($uri, 'GET', $query);
        $request->headers->set('X-Inertia', 'true');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->headers->set('Accept', 'application/json');

        return $request;
    }

    private function toInertiaPayload($inertiaResponse, Request $request): array
    {
        $response = $inertiaResponse->toResponse($request);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue(method_exists($response, 'getData'));

        return $response->getData(true);
    }
}
