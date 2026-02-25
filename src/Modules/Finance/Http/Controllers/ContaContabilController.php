<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Finance\Http\Requests\ContaContabilRequest;
use Modules\Finance\Models\ContaContabil;
use Modules\Finance\Services\ContaContabilService;

class ContaContabilController extends Controller
{
    public function __construct(
        private DataTableService $dt,
        private ContaContabilService $service
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('AccountingAccounts/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'ativo' => (string) $request->query('ativo', ''),
            ],
        ]);
    }

    public function data(Request $request)
    {
        [$query, $columnsMap] = ContaContabil::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('contas_contabeis.edit', $row->id),
                        DataTableActions::status('contas_contabeis.status', 'conta_contabil', $row->id, (bool) $row->st),
                        DataTableActions::delete('contas_contabeis.destroy', $row->id),
                    ]);
                });
            }
        );
    }

    public function create()
    {
        return Inertia::render('AccountingAccounts/Create', [
            'contasPai' => ContaContabil::query()
                ->select('id', 'codigo', 'nome')
                ->orderBy('nome')
                ->get(),
        ]);
    }

    public function store(ContaContabilRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('contas_contabeis.index')->with('success', 'Conta contábil criada com sucesso.');
    }

    public function edit(ContaContabil $conta_contabil)
    {
        return Inertia::render('AccountingAccounts/Edit', [
            'conta' => $conta_contabil,
            'contasPai' => ContaContabil::query()
                ->where('id', '<>', $conta_contabil->id)
                ->select('id', 'codigo', 'nome')
                ->orderBy('nome')
                ->get(),
        ]);
    }

    public function update(ContaContabilRequest $request, ContaContabil $conta_contabil)
    {
        $this->service->update($conta_contabil, $request->validated());

        return redirect()->route('contas_contabeis.index')->with('success', 'Conta contábil atualizada com sucesso.');
    }

    public function destroy(ContaContabil $conta_contabil)
    {
        $this->service->delete($conta_contabil);

        return redirect()->route('contas_contabeis.index')->with('success', 'Conta contábil removida.');
    }
}
