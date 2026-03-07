<?php

namespace Tests\Unit\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Finance\Models\CentroCusto;
use Modules\Finance\Models\ContaContabil;
use Modules\Finance\Models\Despesa;
use Modules\Finance\Repositories\DespesaRepository;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Carbon\Carbon;

#[Group('modularizacao')]
class DespesaRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test makeDatatableQuery for Despesa.
     */
    public function test_make_datatable_query(): void
    {
        $centro = CentroCusto::query()->create(['nome' => 'Centro', 'codigo' => 'C1']);
        $conta = ContaContabil::query()->create(['nome' => 'Conta', 'codigo' => 'CTA', 'tipo' => 'despesa']);

        Despesa::query()->create([
            'descricao' => 'Despesa Ativa',
            'valor' => 100.00,
            'data' => Carbon::now(),
            'centro_custo_id' => $centro->id,
            'conta_contabil_id' => $conta->id,
            'ativo' => 1,
            'status' => 1,
        ]);

        Despesa::query()->create([
            'descricao' => 'Despesa Inativa',
            'valor' => 50.00,
            'data' => Carbon::now(),
            'centro_custo_id' => $centro->id,
            'conta_contabil_id' => $conta->id,
            'ativo' => 0,
            'status' => 0,
        ]);

        /** @var DespesaRepository $repository */
        $repository = app(DespesaRepository::class);

        [$query, $columns] = $repository->makeDatatableQuery(['ativo' => 1]);
        $this->assertGreaterThanOrEqual(1, $query->get()->where('c2', 'Despesa Ativa')->count());

        [$query, $columns] = $repository->makeDatatableQuery(['q' => 'Inativa']);
        $this->assertGreaterThanOrEqual(1, $query->get()->where('c2', 'Despesa Inativa')->count());

        $this->assertIsArray($columns);
        $this->assertArrayHasKey('id', $columns);
    }
}
