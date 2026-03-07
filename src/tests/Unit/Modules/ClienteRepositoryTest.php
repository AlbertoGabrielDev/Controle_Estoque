<?php

namespace Tests\Unit\Modules;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Customers\Models\Cliente;
use Modules\Customers\Models\CustomerSegment;
use Modules\Customers\Repositories\ClienteRepository;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class ClienteRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test makeDatatableQuery for Clientes.
     */
    public function test_make_datatable_query(): void
    {
        $user = User::factory()->create();
        $segment = CustomerSegment::query()->create(['nome' => 'Varejo']);

        Cliente::query()->create([
            'nome' => 'Cliente Ativo',
            'id_users_fk' => $user->id,
            'segment_id' => $segment->id,
            'status' => 1,
            'ativo' => 1,
            'tipo' => 'FISICA',
        ]);

        Cliente::query()->create([
            'nome' => 'Cliente Inativo',
            'id_users_fk' => $user->id,
            'segment_id' => $segment->id,
            'status' => 0,
            'ativo' => 0,
            'tipo' => 'FISICA',
        ]);

        /** @var ClienteRepository $repository */
        $repository = app(ClienteRepository::class);

        [$query, $columns] = $repository->makeDatatableQuery(['ativo' => 1]);
        $this->assertGreaterThanOrEqual(1, $query->get()->where('c2', 'Cliente Ativo')->count());

        [$query, $columns] = $repository->makeDatatableQuery(['q' => 'Inativo']);
        $this->assertGreaterThanOrEqual(1, $query->get()->where('c2', 'Cliente Inativo')->count());

        $this->assertIsArray($columns);
        $this->assertArrayHasKey('id', $columns);
    }
}
