<?php

namespace Tests\Feature;

use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\ProdutoController;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('phase3')]
class Phase3InertiaComponentsTest extends TestCase
{
    public function test_products_index_component_and_filters_contract(): void
    {
        $controller = app(ProdutoController::class);
        $request = $this->makeInertiaRequest('/verdurao/produtos/index', ['q' => 'banana', 'status' => '1']);
        $payload = $this->toInertiaPayload($controller->index($request), $request);

        $this->assertSame('Products/Index', $payload['component']);
        $this->assertSame('banana', $payload['props']['filters']['q'] ?? null);
        $this->assertSame('1', $payload['props']['filters']['status'] ?? null);
    }

    public function test_stock_index_component_and_filters_contract(): void
    {
        $controller = app(EstoqueController::class);
        $request = $this->makeInertiaRequest('/verdurao/estoque', [
            'cod_produto' => '123',
            'nome_produto' => 'banana',
            'status' => '0',
        ]);
        $payload = $this->toInertiaPayload($controller->index($request), $request);

        $this->assertSame('Stock/Index', $payload['component']);
        $this->assertSame('123', $payload['props']['filters']['cod_produto'] ?? null);
        $this->assertSame('banana', $payload['props']['filters']['nome_produto'] ?? null);
        $this->assertSame('0', $payload['props']['filters']['status'] ?? null);
    }

    public function test_phase_three_routes_expose_datatable_and_tax_endpoints(): void
    {
        $this->assertStringContainsString('/verdurao/produtos/data', route('produtos.data'));
        $this->assertStringContainsString('/verdurao/estoque/data', route('estoque.data'));
        $this->assertStringContainsString('/verdurao/estoque/calc-impostos', route('estoque.calcImpostos'));
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
