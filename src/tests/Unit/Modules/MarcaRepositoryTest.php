<?php

namespace Tests\Unit\Modules;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Brands\Models\Marca;
use Modules\Brands\Repositories\MarcaRepository;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class MarcaRepositoryTest extends TestCase
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

        Marca::query()->create([
            'nome_marca' => 'Marca 1',
            'id_users_fk' => $user->id,
            'status' => 1,
        ]);

        $marcaInativa = Marca::query()->create([
            'nome_marca' => 'Marca Inativa',
            'id_users_fk' => $user->id,
        ]);
        $marcaInativa->status = 0;
        $marcaInativa->save();

        /** @var MarcaRepository $repository */
        $repository = app(MarcaRepository::class);

        [$query, $columns] = $repository->makeDatatableQuery(['status' => 1]);
        $this->assertGreaterThanOrEqual(1, $query->where('nome_marca', 'Marca 1')->count());

        [$query, $columns] = $repository->makeDatatableQuery(['status' => 0]);
        $this->assertGreaterThanOrEqual(1, $query->where('nome_marca', 'Marca Inativa')->count());

        [$query, $columns] = $repository->makeDatatableQuery(['q' => 'Inativa']);
        $this->assertGreaterThanOrEqual(1, $query->where('nome_marca', 'Marca Inativa')->count());

        $this->assertIsArray($columns);
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('acoes', $columns);
    }
}
