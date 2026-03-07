<?php

namespace Tests\Unit\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Finance\Models\CentroCusto;
use Modules\Finance\Repositories\CentroCustoRepository;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class CentroCustoRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test makeDatatableQuery for CentroCusto.
     */
    public function test_make_datatable_query(): void
    {
        CentroCusto::query()->create([
            'nome' => 'Centro Administrativo',
            'codigo' => 'ADM1',
            'ativo' => 1,
            'status' => 1,
        ]);

        CentroCusto::query()->create([
            'nome' => 'Centro Operacional',
            'codigo' => 'OPR1',
            'ativo' => 0,
            'status' => 0,
        ]);

        /** @var CentroCustoRepository $repository */
        $repository = app(CentroCustoRepository::class);

        [$query, $columns] = $repository->makeDatatableQuery(['ativo' => 1]);
        $this->assertGreaterThanOrEqual(1, $query->get()->where('c2', 'Centro Administrativo')->count());

        [$query, $columns] = $repository->makeDatatableQuery(['q' => 'Operacional']);
        $this->assertGreaterThanOrEqual(1, $query->get()->where('c2', 'Centro Operacional')->count());

        $this->assertIsArray($columns);
        $this->assertArrayHasKey('id', $columns);
    }

    /**
     * Test getParentOptions.
     */
    public function test_get_parent_options(): void
    {
        $centro1 = CentroCusto::query()->create([
            'nome' => 'Centro 1',
            'codigo' => 'C1',
        ]);

        $centro2 = CentroCusto::query()->create([
            'nome' => 'Centro 2',
            'codigo' => 'C2',
        ]);

        /** @var CentroCustoRepository $repository */
        $repository = app(CentroCustoRepository::class);

        $options = $repository->getParentOptions();
        $this->assertGreaterThanOrEqual(2, $options->count());
        $this->assertTrue($options->contains('id', $centro1->id));
        $this->assertTrue($options->contains('id', $centro2->id));

        $optionsExcluded = $repository->getParentOptions($centro1->id);
        $this->assertFalse($optionsExcluded->contains('id', $centro1->id));
        $this->assertTrue($optionsExcluded->contains('id', $centro2->id));
    }
}
