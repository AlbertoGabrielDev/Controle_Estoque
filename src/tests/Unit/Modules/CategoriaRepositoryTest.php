<?php

namespace Tests\Unit\Modules;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Categories\Models\Categoria;
use Modules\Categories\Repositories\CategoriaRepository;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class CategoriaRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the repository can make datatable query.
     *
     * @return void
     */
    public function test_make_datatable_query(): void
    {
        $user = User::factory()->create();

        Categoria::factory()->create([
            'nome_categoria' => 'Categoria Ativa',
            'id_users_fk' => $user->id,
            'ativo' => 1,
        ]);

        $catInativa = Categoria::factory()->create([
            'nome_categoria' => 'Categoria Inativa',
            'id_users_fk' => $user->id,
            'ativo' => 0,
        ]);

        /** @var CategoriaRepository $repository */
        $repository = app(CategoriaRepository::class);

        [$query, $columns] = $repository->makeDatatableQuery(['status' => 1]);
        $this->assertGreaterThanOrEqual(1, $query->get()->where('nome_categoria', 'Categoria Ativa')->count());

        [$query, $columns] = $repository->makeDatatableQuery(['q' => 'Inativa']);
        $this->assertGreaterThanOrEqual(1, $query->get()->where('nome_categoria', 'Categoria Inativa')->count());

        $this->assertIsArray($columns);
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('acoes', $columns);
    }
}
