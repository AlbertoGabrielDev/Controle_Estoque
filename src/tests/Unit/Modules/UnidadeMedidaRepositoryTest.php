<?php

namespace Tests\Unit\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\MeasureUnits\Models\UnidadeMedida;
use Modules\MeasureUnits\Repositories\UnidadeMedidaRepository;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class UnidadeMedidaRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test makeDatatableQuery for UnidadeMedida.
     */
    public function test_make_datatable_query(): void
    {
        UnidadeMedida::query()->create([
            'codigo' => 'UN',
            'descricao' => 'Unidade',
            'fator_base' => 1.0,
            'ativo' => 1,
            'status' => 1,
        ]);

        UnidadeMedida::query()->create([
            'codigo' => 'CX',
            'descricao' => 'Caixa',
            'fator_base' => 10.0,
            'ativo' => 0,
            'status' => 0,
        ]);

        /** @var UnidadeMedidaRepository $repository */
        $repository = app(UnidadeMedidaRepository::class);

        [$query, $columns] = $repository->makeDatatableQuery(['ativo' => 1]);
        $this->assertGreaterThanOrEqual(1, $query->get()->where('c2', 'Unidade')->count());

        [$query, $columns] = $repository->makeDatatableQuery(['q' => 'Caixa']);
        $this->assertGreaterThanOrEqual(1, $query->get()->where('c2', 'Caixa')->count());

        $this->assertIsArray($columns);
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('c2', $columns);
    }

    /**
     * Test getBaseOptions.
     */
    public function test_get_base_options(): void
    {
        $u1 = UnidadeMedida::query()->create(['codigo' => 'KG', 'descricao' => 'Kilo', 'ativo' => 1]);
        $u2 = UnidadeMedida::query()->create(['codigo' => 'G', 'descricao' => 'Grama', 'ativo' => 1]);

        /** @var UnidadeMedidaRepository $repository */
        $repository = app(UnidadeMedidaRepository::class);

        $options = $repository->getBaseOptions();
        $this->assertGreaterThanOrEqual(2, $options->count());
        $this->assertTrue($options->contains('id', $u1->id));
        $this->assertTrue($options->contains('id', $u2->id));

        $optionsExcluded = $repository->getBaseOptions($u1->id);
        $this->assertFalse($optionsExcluded->contains('id', $u1->id));
        $this->assertTrue($optionsExcluded->contains('id', $u2->id));
    }
}
