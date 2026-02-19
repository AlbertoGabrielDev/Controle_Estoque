<?php

namespace Tests\Feature;

use App\Http\Controllers\SpreadsheetController;
use App\Http\Controllers\VendaController;
use Illuminate\Http\Request;
use Inertia\Response as InertiaResponse;
use PHPUnit\Framework\Attributes\Group;
use ReflectionClass;
use Tests\TestCase;

#[Group('phase4')]
class Phase4InertiaComponentsTest extends TestCase
{
    public function test_sales_index_component_contract(): void
    {
        $response = app(VendaController::class)->vendas();

        $this->assertInstanceOf(InertiaResponse::class, $response);
        $this->assertSame('Sales/Index', $this->readInertiaProperty($response, 'component'));

        $props = $this->readInertiaProperty($response, 'props');
        $this->assertIsArray($props);
        $this->assertArrayHasKey('vendas', $props);
        $this->assertTrue(is_callable($props['vendas']));
    }

    public function test_spreadsheet_index_component_contract(): void
    {
        $controller = app(SpreadsheetController::class);
        $request = $this->makeInertiaRequest('/verdurao/spreadsheet');
        $payload = $this->toInertiaPayload($controller->index(), $request);

        $this->assertSame('Spreadsheets/Index', $payload['component']);
        $this->assertSame(10000, $payload['props']['maxPreviewRows'] ?? null);
        $this->assertSame(20, $payload['props']['maxUploadSizeMb'] ?? null);
        $this->assertNotEmpty($payload['props']['operators'] ?? []);
    }

    public function test_phase_four_routes_expose_sales_and_spreadsheet_endpoints(): void
    {
        $this->assertStringContainsString('/verdurao/vendas', route('vendas.venda'));
        $this->assertStringContainsString('/verdurao/vendas/buscar-produto', route('buscar.produto'));
        $this->assertStringContainsString('/verdurao/vendas/registrar-venda', route('registrar.venda'));
        $this->assertStringContainsString('/verdurao/vendas/carrinho', route('carrinho.venda'));

        $this->assertStringContainsString('/verdurao/spreadsheet', route('spreadsheet.index'));
        $this->assertStringContainsString('/verdurao/spreadsheet/upload', route('spreadsheet.upload'));
        $this->assertStringContainsString('/verdurao/spreadsheet/compare', route('spreadsheet.compare'));
        $this->assertStringContainsString('/verdurao/spreadsheet/data/test.csv', route('spreadsheet.data', ['filename' => 'test.csv']));
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

    private function readInertiaProperty(InertiaResponse $response, string $property): mixed
    {
        $reflection = new ReflectionClass($response);
        $prop = $reflection->getProperty($property);
        $prop->setAccessible(true);

        return $prop->getValue($response);
    }
}
