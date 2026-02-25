<?php

namespace Tests\Feature;

use App\Http\Controllers\CustomerSegmentController;
use App\Models\CustomerSegment;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('phase1')]
class ExampleTest extends TestCase
{
    public function test_customer_segments_index_component_and_filters_contract(): void
    {
        $controller = app(CustomerSegmentController::class);
        $request = $this->makeInertiaRequest('/verdurao/clientes/segmentos', ['q' => 'vip']);
        $payload = $this->toInertiaPayload($controller->index($request), $request);

        $this->assertSame('Segments/Index', $payload['component']);
        $this->assertSame('vip', $payload['props']['filters']['q'] ?? null);
    }

    public function test_customer_segments_create_component_contract(): void
    {
        $controller = app(CustomerSegmentController::class);
        $request = $this->makeInertiaRequest('/verdurao/clientes/segmentos/create');
        $payload = $this->toInertiaPayload($controller->create(), $request);

        $this->assertSame('Segments/Create', $payload['component']);
    }

    public function test_customer_segments_edit_component_contract(): void
    {
        $controller = app(CustomerSegmentController::class);
        $request = $this->makeInertiaRequest('/verdurao/clientes/segmentos/1');
        $segment = new CustomerSegment([
            'id' => 1,
            'nome' => 'Segmento Teste',
        ]);

        $payload = $this->toInertiaPayload($controller->edit($segment), $request);

        $this->assertSame('Segments/Edit', $payload['component']);
        $this->assertSame('Segmento Teste', $payload['props']['segmento']['nome'] ?? null);
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
