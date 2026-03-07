<?php

namespace Tests\Unit\Modules;

use App\Models\UnidadeMedida;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Categories\Models\Categoria;
use Modules\Items\Models\Item;
use Modules\Items\Repositories\ItemRepository;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class ItemRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test makeDatatableQuery for Item.
     */
    public function test_make_datatable_query(): void
    {
        $user = User::factory()->create();

        $categoria = Categoria::factory()->create([
            'nome_categoria' => 'Eletrônicos',
            'tipo' => 'produto',
            'id_users_fk' => $user->id,
            'ativo' => 1
        ]);
        $unidade = UnidadeMedida::query()->create(['codigo' => 'UN', 'descricao' => 'Unidade', 'ativo' => 1]);

        Item::query()->create([
            'sku' => 'SKU-001',
            'nome' => 'Item Ativo',
            'tipo' => 'produto',
            'categoria_id' => $categoria->id_categoria, // Note it's id_categoria
            'unidade_medida_id' => $unidade->id,
            'custo' => 50.00,
            'preco_base' => 100.00,
            'peso_kg' => 1.5,
            'volume_m3' => 0.05,
            'controla_estoque' => 1,
            'ativo' => 1,
        ]);

        Item::query()->create([
            'sku' => 'SKU-002',
            'nome' => 'Item Inativo',
            'tipo' => 'produto',
            'categoria_id' => $categoria->id_categoria,
            'unidade_medida_id' => $unidade->id,
            'custo' => 30.00,
            'preco_base' => 60.00,
            'peso_kg' => 0.5,
            'volume_m3' => 0.01,
            'controla_estoque' => 1,
            'ativo' => 0,
        ]);

        /** @var ItemRepository $repository */
        $repository = app(ItemRepository::class);

        [$query, $columns] = $repository->makeDatatableQuery(['ativo' => 1]);
        $this->assertGreaterThanOrEqual(1, $query->get()->where('c2', 'Item Ativo')->count());

        [$query, $columns] = $repository->makeDatatableQuery(['q' => 'Inativo']);
        $this->assertGreaterThanOrEqual(1, $query->get()->where('c2', 'Item Inativo')->count());

        $this->assertIsArray($columns);
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('c2', $columns);
    }
}
