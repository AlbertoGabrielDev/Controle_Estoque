<?php

namespace Tests\Feature\Modules;

use App\Services\StockService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery\MockInterface;
use Modules\Sales\Http\Controllers\Api\CartController;
use Modules\Sales\Http\Requests\CartUpsertRequest;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class SalesModulePhaseThreeFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->prepareCartSchema();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');

        parent::tearDown();
    }

    public function test_sales_cart_api_upsert_and_remove_flow_updates_cart_totals(): void
    {
        $this->mock(StockService::class, function (MockInterface $mock) {
            $mock->shouldReceive('checkAndPrice')
                ->once()
                ->withArgs(function (array $items) {
                    return count($items) === 2
                        && ($items[0]['sku'] ?? null) === 'ABC'
                        && (int) ($items[0]['qty'] ?? 0) === 2;
                })
                ->andReturn([
                    'linhas' => [
                        [
                            'cod_produto' => 'ABC',
                            'nome_produto' => 'Produto ABC',
                            'preco_unit' => 10.00,
                            'quantidade' => 2,
                            'subtotal_valor' => 20.00,
                        ],
                        [
                            'cod_produto' => 'XYZ',
                            'nome_produto' => 'Produto XYZ',
                            'preco_unit' => 5.00,
                            'quantidade' => 2,
                            'subtotal_valor' => 10.00,
                        ],
                    ],
                    'total_valor' => 30.00,
                ]);
        });

        $controller = app(CartController::class);

        $upsertRequest = CartUpsertRequest::create('/api/carts/upsert', 'POST', [
            'client' => '5511999999999',
            'items' => [
                ['sku' => 'ABC', 'qty' => 2],
                ['sku' => 'XYZ', 'qty' => 2],
            ],
        ]);
        $upsert = $controller->upsert($upsertRequest);
        $upsertData = $upsert->getData(true);

        $this->assertSame(200, $upsert->getStatusCode());
        $this->assertSame('5511999999999', $upsertData['client'] ?? null);
        $this->assertCount(2, $upsertData['items'] ?? []);

        $this->assertSame(1, DB::table('carts')->count());
        $this->assertSame(2, DB::table('cart_items')->count());
        $this->assertEquals(30.00, (float) DB::table('carts')->value('total_valor'));

        $remove = $controller->remove(Request::create('/api/carts/remove', 'POST', [
            'client' => '5511999999999',
            'items' => [
                ['sku' => 'ABC', 'qty' => 1],
            ],
        ]));
        $removeData = $remove->getData(true);

        $this->assertSame(200, $remove->getStatusCode());
        $this->assertSame('5511999999999', $removeData['client'] ?? null);
        $this->assertSame('ABC', data_get($removeData, 'removed.0.cod_produto'));
        $this->assertSame('decremented', data_get($removeData, 'removed.0.action'));
        $this->assertSame(1, (int) data_get($removeData, 'removed.0.new_qty'));

        $this->assertEquals(20.00, (float) DB::table('carts')->value('total_valor'));
        $this->assertSame(1, (int) DB::table('cart_items')->where('cod_produto', 'ABC')->value('quantidade'));
        $this->assertSame(2, (int) DB::table('cart_items')->where('cod_produto', 'XYZ')->value('quantidade'));

        $get = $controller->getByclient('5511999999999');
        $getData = $get->getData(true);
        $this->assertSame(200, $get->getStatusCode());
        $this->assertSame('5511999999999', $getData['client'] ?? null);
        $this->assertCount(2, $getData['items'] ?? []);
    }

    private function prepareCartSchema(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');

        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('client', 20)->index();
            $table->string('status', 20)->default('open');
            $table->decimal('total_valor', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id')->index();
            $table->unsignedSmallInteger('id_estoque_fk')->nullable();
            $table->string('cod_produto', 60)->index();
            $table->string('nome_produto', 60);
            $table->decimal('preco_unit', 10, 2);
            $table->unsignedInteger('quantidade');
            $table->decimal('subtotal_valor', 10, 2);
            $table->timestamps();
        });
    }
}
