<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Fornecedor;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Finance\Http\Requests\DespesaRequest;
use Modules\Finance\Models\CentroCusto;
use Modules\Finance\Models\ContaContabil;
use Modules\Finance\Models\Despesa;
use Modules\Finance\Services\DespesaService;

class DespesaController extends Controller
{
    public function __construct(
        private DataTableService $dt,
        private DespesaService $service
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('Expenses/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'ativo' => (string) $request->query('ativo', ''),
                'centro_custo_id' => (string) $request->query('centro_custo_id', ''),
                'conta_contabil_id' => (string) $request->query('conta_contabil_id', ''),
                'fornecedor_id' => (string) $request->query('fornecedor_id', ''),
                'data_inicio' => (string) $request->query('data_inicio', ''),
                'data_fim' => (string) $request->query('data_fim', ''),
            ],
            ...$this->resolveFormOptions(),
        ]);
    }

    public function data(Request $request)
    {
        [$query, $columnsMap] = Despesa::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('despesas.edit', $row->id),
                        DataTableActions::status('despesas.status', 'despesa', $row->id, (bool) $row->st),
                        DataTableActions::delete('despesas.destroy', $row->id),
                    ]);
                });
            }
        );
    }

    public function create()
    {
        return Inertia::render('Expenses/Create', $this->resolveFormOptions());
    }

    public function store(DespesaRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('despesas.index')->with('success', 'Despesa criada com sucesso.');
    }

    public function edit(Despesa $despesa)
    {
        return Inertia::render('Expenses/Edit', [
            'despesa' => $despesa,
            ...$this->resolveFormOptions(),
        ]);
    }

    public function update(DespesaRequest $request, Despesa $despesa)
    {
        $this->service->update($despesa, $request->validated());

        return redirect()->route('despesas.index')->with('success', 'Despesa atualizada com sucesso.');
    }

    public function destroy(Despesa $despesa)
    {
        $this->service->delete($despesa);

        return redirect()->route('despesas.index')->with('success', 'Despesa removida.');
    }

    private function resolveFormOptions(): array
    {
        $centros = CentroCusto::query()
            ->select('id', 'codigo', 'nome')
            ->where('ativo', 1)
            ->orderBy('nome')
            ->get();

        $contas = ContaContabil::query()
            ->select('id', 'codigo', 'nome')
            ->where('ativo', 1)
            ->where('aceita_lancamento', 1)
            ->where('tipo', 'despesa')
            ->orderBy('nome')
            ->get();

        $fornecedores = Fornecedor::query()
            ->select('id_fornecedor', 'nome_fornecedor', 'razao_social')
            ->where('ativo', 1)
            ->orderBy('nome_fornecedor')
            ->get();

        return [
            'centrosCusto' => $centros,
            'contasContabeis' => $contas,
            'fornecedores' => $fornecedores,
        ];
    }
}
