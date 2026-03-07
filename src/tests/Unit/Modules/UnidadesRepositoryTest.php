<?php

namespace Tests\Unit\Modules;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Units\Models\Unidades;
use Modules\Units\Repositories\UnidadesRepository;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class UnidadesRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test makeDatatableQuery for Unidades.
     */
    public function test_make_datatable_query(): void
    {
        $user = User::factory()->create();

        Unidades::query()->create([
            'nome' => 'Matriz',
            'id_users_fk' => $user->id,
            'status' => 1,
        ]);

        Unidades::query()->create([
            'nome' => 'Filial',
            'id_users_fk' => $user->id,
            'status' => 0,
        ]);

        /** @var UnidadesRepository $repository */
        $repository = app(UnidadesRepository::class);

        [$query, $columns] = $repository->makeDatatableQuery(['status' => 1]);
        $this->assertGreaterThanOrEqual(1, $query->get()->where('c1', 'Matriz')->count());

        [$query, $columns] = $repository->makeDatatableQuery(['q' => 'Filial']);
        $this->assertGreaterThanOrEqual(1, $query->get()->where('c1', 'Filial')->count());

        $this->assertIsArray($columns);
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('c1', $columns);
    }
}
