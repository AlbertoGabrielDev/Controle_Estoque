<?php

namespace Tests\Feature;

use App\Services\VendaService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('priceflow')]
class VendaTabelaPrecoEstoqueIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->rebuildSchemaForVendaFlow();
        $this->seedBaseVendaFlowScenario();

        // VendaService grava id_unidade_fk usando este helper global.
        app()->instance('current.unidade', (object) ['id_unidade' => 1]);
    }

    public function test_manual_search_returns_stock_options_with_price_table_per_lot_context(): void
    {
        $service = app(VendaService::class);

        $result = $service->buscarProduto(
            codigoQr: null,
            codigoProd: 'PROD-001',
            client: '5511999999999'
        );

        $this->assertNull($result['produto']);
        $this->assertCount(2, $result['opcoes']);

        $optionsByStock = [];
        foreach ($result['opcoes'] as $option) {
            $optionsByStock[(int) $option['id_estoque']] = $option;
        }

        $this->assertArrayHasKey(101, $optionsByStock);
        $this->assertArrayHasKey(102, $optionsByStock);

        // Lote 101 (marca/fornecedor específicos) usa preço específico da tabela.
        $this->assertSame(22.0, (float) $optionsByStock[101]['preco_venda']);

        // Lote 102 cai na regra genérica do mesmo produto.
        $this->assertSame(30.0, (float) $optionsByStock[102]['preco_venda']);
    }

    public function test_qr_sale_applies_price_table_and_decrements_correct_stock_lot(): void
    {
        $service = app(VendaService::class);

        $lookup = $service->buscarProduto(
            codigoQr: 'QR-LOTE-A',
            codigoProd: null,
            client: '5511999999999'
        );

        $this->assertNotNull($lookup['produto']);
        $this->assertSame(101, (int) $lookup['produto']['id_estoque']);
        $this->assertSame(22.0, (float) $lookup['produto']['preco_venda']);

        $cart = $service->adicionarItem(
            client: '5511999999999',
            idProduto: 1,
            quantidade: 2,
            idEstoque: 101
        );

        $this->assertCount(1, $cart->items);
        $this->assertSame(101, (int) $cart->items[0]->id_estoque_fk);
        $this->assertSame(22.0, (float) $cart->items[0]->preco_unit);

        $final = $service->finalizarVendaDoCarrinho('5511999999999');

        $this->assertTrue((bool) ($final['ok'] ?? false));
        $this->assertSame('created', $final['status'] ?? null);

        // Baixou somente o lote vendido via QR.
        $this->assertSame(3, (int) DB::table('estoques')->where('id_estoque', 101)->value('quantidade'));
        $this->assertSame(7, (int) DB::table('estoques')->where('id_estoque', 102)->value('quantidade'));

        $venda = DB::table('vendas')->first();
        $this->assertNotNull($venda);
        $this->assertSame(101, (int) $venda->id_estoque_fk);
        $this->assertSame(1, (int) $venda->id_produto_fk);
        $this->assertSame(22.0, (float) $venda->preco_venda);

        $orderItem = DB::table('order_items')->first();
        $this->assertNotNull($orderItem);
        $this->assertSame(2, (int) $orderItem->quantidade);
        $this->assertSame(44.0, (float) $orderItem->sub_valor);
    }

    private function rebuildSchemaForVendaFlow(): void
    {
        foreach ([
            'vendas',
            'order_items',
            'orders',
            'cart_items',
            'carts',
            'tabela_preco_itens',
            'tabelas_preco',
            'estoques',
            'clientes',
            'produtos',
            'fornecedores',
            'marcas',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::create('marcas', function (Blueprint $table) {
            $table->increments('id_marca');
            $table->string('nome_marca');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        Schema::create('fornecedores', function (Blueprint $table) {
            $table->increments('id_fornecedor');
            $table->string('nome_fornecedor');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        Schema::create('produtos', function (Blueprint $table) {
            $table->increments('id_produto');
            $table->string('cod_produto', 60)->unique();
            $table->string('nome_produto', 120);
            $table->string('unidade_medida', 20)->nullable();
            $table->unsignedBigInteger('unidade_medida_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        Schema::create('clientes', function (Blueprint $table) {
            $table->increments('id_cliente');
            $table->string('whatsapp', 30)->nullable();
            $table->unsignedInteger('tabela_preco_id')->nullable();
            $table->string('tabela_preco', 60)->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('ativo')->default(1);
            $table->boolean('bloqueado')->default(0);
            $table->timestamps();
        });

        Schema::create('tabelas_preco', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo', 60)->unique();
            $table->string('nome', 120);
            $table->string('tipo_alvo', 20)->default('produto');
            $table->string('moeda', 8)->default('EUR');
            $table->date('inicio_vigencia')->nullable();
            $table->date('fim_vigencia')->nullable();
            $table->boolean('ativo')->default(1);
            $table->timestamps();
        });

        Schema::create('tabela_preco_itens', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tabela_preco_id');
            $table->unsignedInteger('item_id')->nullable();
            $table->unsignedInteger('produto_id')->nullable();
            $table->unsignedInteger('marca_id')->nullable();
            $table->unsignedInteger('fornecedor_id')->nullable();
            $table->decimal('preco', 10, 2);
            $table->decimal('desconto_percent', 5, 2)->default(0);
            $table->unsignedInteger('quantidade_minima')->default(1);
            $table->timestamps();
        });

        Schema::create('estoques', function (Blueprint $table) {
            $table->increments('id_estoque');
            $table->unsignedInteger('id_produto_fk');
            $table->unsignedInteger('id_fornecedor_fk')->nullable();
            $table->unsignedInteger('id_marca_fk')->nullable();
            $table->decimal('preco_venda', 10, 2)->default(0);
            $table->unsignedInteger('quantidade')->default(0);
            $table->boolean('status')->default(1);
            $table->string('qrcode', 80)->nullable();
            $table->date('data_chegada')->nullable();
            $table->timestamps();
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client', 30);
            $table->string('status', 20)->default('open');
            $table->decimal('total_valor', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cart_id');
            $table->unsignedInteger('id_estoque_fk')->nullable();
            $table->string('cod_produto', 60);
            $table->string('nome_produto', 120);
            $table->decimal('preco_unit', 10, 2);
            $table->unsignedInteger('quantidade');
            $table->decimal('subtotal_valor', 10, 2);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client', 30);
            $table->unsignedInteger('cart_id')->nullable();
            $table->string('status', 20)->default('created');
            $table->decimal('total_valor', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->string('cod_produto', 60);
            $table->string('nome_produto', 120);
            $table->decimal('preco_unit', 10, 2);
            $table->unsignedInteger('quantidade');
            $table->decimal('sub_valor', 10, 2);
            $table->timestamps();
        });

        Schema::create('vendas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_produto_fk');
            $table->unsignedInteger('id_estoque_fk')->nullable();
            $table->unsignedBigInteger('id_usuario_fk')->nullable();
            $table->unsignedInteger('quantidade');
            $table->decimal('preco_venda', 10, 2);
            $table->string('cod_produto', 60);
            $table->string('unidade_medida', 20)->nullable();
            $table->string('nome_produto', 120);
            $table->unsignedInteger('id_unidade_fk')->nullable();
            $table->timestamps();
        });
    }

    private function seedBaseVendaFlowScenario(): void
    {
        $now = Carbon::now();

        DB::table('marcas')->insert([
            ['id_marca' => 1, 'nome_marca' => 'Marca A', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id_marca' => 2, 'nome_marca' => 'Marca B', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('fornecedores')->insert([
            ['id_fornecedor' => 1, 'nome_fornecedor' => 'Fornecedor A', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id_fornecedor' => 2, 'nome_fornecedor' => 'Fornecedor B', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('produtos')->insert([
            'id_produto' => 1,
            'cod_produto' => 'PROD-001',
            'nome_produto' => 'Arroz Teste',
            'unidade_medida' => 'KG',
            'unidade_medida_id' => null,
            'item_id' => null,
            'status' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('tabelas_preco')->insert([
            'id' => 1,
            'codigo' => 'TP-CLIENTE',
            'nome' => 'Tabela Cliente Teste',
            'tipo_alvo' => 'produto',
            'moeda' => 'BRL',
            'inicio_vigencia' => null,
            'fim_vigencia' => null,
            'ativo' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('clientes')->insert([
            'id_cliente' => 1,
            'whatsapp' => '5511999999999',
            'tabela_preco_id' => 1,
            'tabela_preco' => null,
            'status' => 1,
            'ativo' => 1,
            'bloqueado' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('estoques')->insert([
            [
                'id_estoque' => 101,
                'id_produto_fk' => 1,
                'id_fornecedor_fk' => 1,
                'id_marca_fk' => 1,
                'preco_venda' => 50.00,
                'quantidade' => 5,
                'status' => 1,
                'qrcode' => 'QR-LOTE-A',
                'data_chegada' => '2026-02-20',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_estoque' => 102,
                'id_produto_fk' => 1,
                'id_fornecedor_fk' => 2,
                'id_marca_fk' => 2,
                'preco_venda' => 60.00,
                'quantidade' => 7,
                'status' => 1,
                'qrcode' => 'QR-LOTE-B',
                'data_chegada' => '2026-02-21',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('tabela_preco_itens')->insert([
            [
                'id' => 1,
                'tabela_preco_id' => 1,
                'produto_id' => 1,
                'item_id' => null,
                'marca_id' => null,
                'fornecedor_id' => null,
                'preco' => 30.00,
                'desconto_percent' => 0,
                'quantidade_minima' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'tabela_preco_id' => 1,
                'produto_id' => 1,
                'item_id' => null,
                'marca_id' => 1,
                'fornecedor_id' => 1,
                'preco' => 22.00,
                'desconto_percent' => 0,
                'quantidade_minima' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}

