<?php

namespace Tests\Unit\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Finance\Models\ContaContabil;
use Modules\Finance\Repositories\ContaContabilRepository;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class ContaContabilRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test makeDatatableQuery for ContaContabil.
     */
    public function test_make_datatable_query(): void
    {
        ContaContabil::query()->create([
            'nome' => 'Conta Ativa',
            'codigo' => 'CTA1',
            'tipo' => 'receita',
            'ativo' => 1,
            'status' => 1,
        ]);

        ContaContabil::query()->create([
            'nome' => 'Conta Inativa',
            'codigo' => 'CTA2',
            'tipo' => 'despesa',
            'ativo' => 0,
            'status' => 0,
        ]);

        /** @var ContaContabilRepository $repository */
        $repository = app(ContaContabilRepository::class);

        [$query, $columns] = $repository->makeDatatableQuery(['ativo' => 1]);
        $this->assertGreaterThanOrEqual(1, $query->get()->where('c2', 'Conta Ativa')->count());

        [$query, $columns] = $repository->makeDatatableQuery(['q' => 'Inativa']);
        $this->assertGreaterThanOrEqual(1, $query->get()->where('c2', 'Conta Inativa')->count());

        $this->assertIsArray($columns);
        $this->assertArrayHasKey('id', $columns);
        // acoes is computed so it isn't returned by HasDatatableConfig
    }

    /**
     * Test getParentOptions.
     */
    public function test_get_parent_options(): void
    {
        $conta1 = ContaContabil::query()->create([
            'nome' => 'Conta 1',
            'codigo' => 'C1',
        ]);

        $conta2 = ContaContabil::query()->create([
            'nome' => 'Conta 2',
            'codigo' => 'C2',
        ]);

        /** @var ContaContabilRepository $repository */
        $repository = app(ContaContabilRepository::class);

        $options = $repository->getParentOptions();
        $this->assertGreaterThanOrEqual(2, $options->count());
        $this->assertTrue($options->contains('id', $conta1->id));
        $this->assertTrue($options->contains('id', $conta2->id));

        $optionsExcluded = $repository->getParentOptions($conta1->id);
        $this->assertFalse($optionsExcluded->contains('id', $conta1->id));
        $this->assertTrue($optionsExcluded->contains('id', $conta2->id));
    }
}
