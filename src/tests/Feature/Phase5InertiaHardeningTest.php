<?php

namespace Tests\Feature;

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EstoqueController;
use Inertia\Response as InertiaResponse;
use PHPUnit\Framework\Attributes\Group;
use ReflectionClass;
use Tests\TestCase;

#[Group('phase5')]
class Phase5InertiaHardeningTest extends TestCase
{
    public function test_category_products_and_stock_history_render_inertia_with_lazy_props(): void
    {
        $categoryResponse = app(CategoriaController::class)->produto(1);

        $this->assertInstanceOf(InertiaResponse::class, $categoryResponse);
        $this->assertSame('Categories/Products', $this->readInertiaProperty($categoryResponse, 'component'));
        $categoryProps = $this->readInertiaProperty($categoryResponse, 'props');
        $this->assertArrayHasKey('categoria', $categoryProps);
        $this->assertArrayHasKey('produtos', $categoryProps);
        $this->assertTrue(is_callable($categoryProps['categoria']));
        $this->assertTrue(is_callable($categoryProps['produtos']));

        $stockResponse = app(EstoqueController::class)->historico();

        $this->assertInstanceOf(InertiaResponse::class, $stockResponse);
        $this->assertSame('Stock/History', $this->readInertiaProperty($stockResponse, 'component'));
        $stockProps = $this->readInertiaProperty($stockResponse, 'props');
        $this->assertArrayHasKey('historicos', $stockProps);
        $this->assertTrue(is_callable($stockProps['historicos']));
    }

    public function test_phase_five_routes_keep_hardening_contracts(): void
    {
        $this->assertStringContainsString('/verdurao/categoria/produto/1', route('categorias.produto', ['categoria' => 1]));
        $this->assertStringContainsString('/verdurao/estoque/historico', route('estoque.historico'));
        $this->assertStringContainsString('/verdurao/spreadsheet', route('spreadsheet.index'));
        $this->assertStringContainsString('/verdurao/vendas/vendas', route('vendas.historico_vendas'));
    }

    private function readInertiaProperty(InertiaResponse $response, string $property): mixed
    {
        $reflection = new ReflectionClass($response);
        $prop = $reflection->getProperty($property);
        $prop->setAccessible(true);

        return $prop->getValue($response);
    }
}
