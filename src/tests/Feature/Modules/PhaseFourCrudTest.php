<?php

namespace Tests\Feature\Modules;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Brands\Models\Marca;
use Modules\Categories\Models\Categoria;
use Modules\Customers\Models\Cliente;
use Modules\Customers\Models\CustomerSegment;
use Modules\Items\Models\Item;
use Modules\MeasureUnits\Models\UnidadeMedida;
use Modules\Suppliers\Models\Fornecedor;
use Modules\Units\Models\Unidades;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class PhaseFourCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_brands_crud(): void
    {
        $user = User::factory()->create();

        $marca = Marca::query()->create([
            'nome_marca' => 'Marca ' . Str::random(6),
            'id_users_fk' => $user->id,
        ]);

        $this->assertDatabaseHas('marcas', ['id_marca' => $marca->id_marca]);

        $marca->update(['nome_marca' => 'Marca ' . Str::random(6)]);
        $this->assertDatabaseHas('marcas', ['id_marca' => $marca->id_marca]);

        $marca->delete();
        $this->assertDatabaseMissing('marcas', ['id_marca' => $marca->id_marca]);
    }

    public function test_categories_crud(): void
    {
        $user = User::factory()->create();

        $categoria = Categoria::query()->create([
            'codigo' => 'CAT' . Str::random(4),
            'nome_categoria' => 'Categoria ' . Str::random(6),
            'imagem' => 'categoria.png',
            'id_users_fk' => $user->id,
            'ativo' => true,
        ]);

        $this->assertDatabaseHas('categorias', ['id_categoria' => $categoria->id_categoria]);

        $categoria->update(['nome_categoria' => 'Categoria ' . Str::random(6)]);
        $this->assertDatabaseHas('categorias', ['id_categoria' => $categoria->id_categoria]);

        $categoria->delete();
        $this->assertDatabaseMissing('categorias', ['id_categoria' => $categoria->id_categoria]);
    }

    public function test_items_crud(): void
    {
        $item = Item::query()->create([
            'sku' => 'SKU-' . Str::random(8),
            'nome' => 'Item ' . Str::random(6),
            'tipo' => 'produto',
            'preco_base' => 10.5,
            'custo' => 5.5,
            'ativo' => true,
        ]);

        $this->assertDatabaseHas('itens', ['id' => $item->id]);

        $item->update(['nome' => 'Item ' . Str::random(6)]);
        $this->assertDatabaseHas('itens', ['id' => $item->id]);

        $item->delete();
        $this->assertDatabaseMissing('itens', ['id' => $item->id]);
    }

    public function test_measure_units_crud(): void
    {
        $unidade = UnidadeMedida::query()->create([
            'codigo' => 'UN' . Str::random(4),
            'descricao' => 'Unidade ' . Str::random(6),
            'fator_base' => 1,
            'ativo' => true,
        ]);

        $this->assertDatabaseHas('unidades_medida', ['id' => $unidade->id]);

        $unidade->update(['descricao' => 'Unidade ' . Str::random(6)]);
        $this->assertDatabaseHas('unidades_medida', ['id' => $unidade->id]);

        $unidade->delete();
        $this->assertDatabaseMissing('unidades_medida', ['id' => $unidade->id]);
    }

    public function test_units_crud(): void
    {
        $user = User::factory()->create();

        $unidade = Unidades::query()->create([
            'nome' => 'Unidade ' . Str::random(6),
            'status' => 1,
            'id_users_fk' => $user->id,
        ]);

        $this->assertDatabaseHas('unidades', ['id_unidade' => $unidade->id_unidade]);

        $unidade->update(['nome' => 'Unidade ' . Str::random(6)]);
        $this->assertDatabaseHas('unidades', ['id_unidade' => $unidade->id_unidade]);

        $unidade->delete();
        $this->assertDatabaseMissing('unidades', ['id_unidade' => $unidade->id_unidade]);
    }

    public function test_suppliers_crud(): void
    {
        $user = User::factory()->create();
        $suffix = Str::random(6);

        $fornecedor = Fornecedor::query()->create([
            'codigo' => 'FOR' . $suffix,
            'razao_social' => 'Fornecedor ' . $suffix,
            'nome_fornecedor' => 'Fornecedor ' . $suffix,
            'cnpj' => '12.345.678/0001-' . random_int(10, 99),
            'nif_cif' => 'NIF' . $suffix,
            'cep' => '01001-000',
            'logradouro' => 'Rua A',
            'bairro' => 'Centro',
            'numero_casa' => '123',
            'cidade' => 'Sao Paulo',
            'uf' => 'SP',
            'email' => 'fornecedor_' . $suffix . '@example.com',
            'id_users_fk' => $user->id,
            'ativo' => true,
            'status' => 1,
        ]);

        $this->assertDatabaseHas('fornecedores', ['id_fornecedor' => $fornecedor->id_fornecedor]);

        $fornecedor->update(['bairro' => 'Centro 2']);
        $this->assertDatabaseHas('fornecedores', ['id_fornecedor' => $fornecedor->id_fornecedor, 'bairro' => 'Centro 2']);

        $fornecedor->delete();
        $this->assertDatabaseMissing('fornecedores', ['id_fornecedor' => $fornecedor->id_fornecedor]);
    }

    public function test_customers_crud(): void
    {
        $cliente = Cliente::query()->create([
            'nome' => 'Cliente ' . Str::random(6),
            'tipo_pessoa' => 'PF',
            'documento' => '123.456.789-00',
            'whatsapp' => '+55 11 90000-0000',
            'ativo' => true,
            'status' => 1,
        ]);

        $this->assertDatabaseHas('clientes', ['id_cliente' => $cliente->id_cliente]);

        $cliente->update(['nome' => 'Cliente ' . Str::random(6)]);
        $this->assertDatabaseHas('clientes', ['id_cliente' => $cliente->id_cliente]);

        $cliente->delete();
        $this->assertDatabaseMissing('clientes', ['id_cliente' => $cliente->id_cliente]);
    }

    public function test_segments_crud(): void
    {
        $segment = CustomerSegment::query()->create([
            'nome' => 'Segmento ' . Str::random(6),
        ]);

        $this->assertDatabaseHas('customer_segments', ['id' => $segment->id]);

        $segment->update(['nome' => 'Segmento ' . Str::random(6)]);
        $this->assertDatabaseHas('customer_segments', ['id' => $segment->id]);

        $segment->delete();
        $this->assertDatabaseMissing('customer_segments', ['id' => $segment->id]);
    }
}
