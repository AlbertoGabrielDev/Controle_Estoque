<?php

namespace Tests\Unit\Modules;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Customers\Models\CustomerSegment;
use Modules\Customers\Repositories\CustomerSegmentRepository;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class CustomerSegmentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test makeDatatableQuery for segments.
     */
    public function test_make_datatable_query(): void
    {
        CustomerSegment::query()->create([
            'nome' => 'Segmento Ativo',
        ]);

        CustomerSegment::query()->create([
            'nome' => 'Segmento Inativo',
        ]);

        /** @var CustomerSegmentRepository $repository */
        $repository = app(CustomerSegmentRepository::class);

        [$query, $columns] = $repository->makeDatatableQuery(['q' => 'Ativo']);
        $this->assertGreaterThanOrEqual(1, $query->where('nome', 'Segmento Ativo')->count());

        [$query, $columns] = $repository->makeDatatableQuery(['q' => 'Inativo']);
        $this->assertGreaterThanOrEqual(1, $query->where('nome', 'Segmento Inativo')->count());

        $this->assertIsArray($columns);
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('acoes', $columns);
    }
}
