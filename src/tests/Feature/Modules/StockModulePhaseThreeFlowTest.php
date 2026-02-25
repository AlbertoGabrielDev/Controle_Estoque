<?php

namespace Tests\Feature\Modules;

use Mockery\MockInterface;
use Illuminate\Http\Request;
use Modules\Stock\Http\Controllers\EstoqueController;
use Modules\Stock\Services\EstoqueService;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class StockModulePhaseThreeFlowTest extends TestCase
{
    public function test_stock_calc_impostos_route_returns_preview_payload_and_rendered_html(): void
    {
        $payload = [
            'vm' => [
                '__totais' => [
                    'preco_base' => 10.0,
                    'total_impostos' => 2.5,
                    'total_com_impostos' => 12.5,
                ],
                'impostos' => [
                    [
                        'imposto' => 'ICMS',
                        'nome' => 'ICMS Teste',
                        'total' => 2.5,
                        'linhas' => [],
                    ],
                ],
            ],
            'raw' => ['_total_impostos' => 2.5],
            'meta' => [
                'total_com_impostos' => 12.5,
                'id_tax_fk' => 99,
            ],
        ];

        $this->mock(EstoqueService::class, function (MockInterface $mock) use ($payload) {
            $mock->shouldReceive('calcularImpostosPreview')
                ->once()
                ->withArgs(function (array $input) {
                    return (int) ($input['id_produto_fk'] ?? 0) === 123
                        && (float) ($input['preco_venda'] ?? 0) === 10.0;
                })
                ->andReturn($payload);
        });

        $controller = app(EstoqueController::class);
        $request = Request::create('/verdurao/estoque/calc-impostos', 'POST', [
            'id_produto_fk' => 123,
            'preco_venda' => 10.00,
        ]);

        $response = $controller->calcImpostos($request);
        $data = $response->getData(true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(99, data_get($data, 'meta.id_tax_fk'));
        $this->assertSame(12.5, (float) data_get($data, 'meta.total_com_impostos'));
        $this->assertSame(2.5, (float) data_get($data, 'vm.__totais.total_impostos'));

        $html = (string) ($data['html'] ?? '');
        $this->assertStringContainsString('impostos-wrap', $html);
        $this->assertStringContainsString('ICMS', $html);
    }
}
