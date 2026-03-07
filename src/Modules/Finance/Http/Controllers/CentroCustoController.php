<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Finance\Http\Requests\CentroCustoRequest;
use Modules\Finance\Models\CentroCusto;
use Modules\Finance\Repositories\CentroCustoRepository;
use Modules\Finance\Services\CentroCustoService;

class CentroCustoController extends Controller
{
    public function __construct(
        private DataTableService $dt,
        private CentroCustoService $service,
        private CentroCustoRepository $repository
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('CostCenters/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'ativo' => (string) $request->query('ativo', ''),
            ],
        ]);
    }

    public function data(Request $request)
    {
        [$query, $columnsMap] = $this->repository->makeDatatableQuery($request->all());

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('centros_custo.edit', $row->id),
                        DataTableActions::status('centros_custo.status', 'centro_custo', $row->id, (bool) $row->st),
                        DataTableActions::delete('centros_custo.destroy', $row->id),
                    ]);
                });
            }
        );
    }

    public function create()
    {
        return Inertia::render('CostCenters/Create', [
            'centrosPai' => $this->repository->getParentOptions(),
        ]);
    }

    public function store(CentroCustoRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('centros_custo.index')->with('success', 'Centro de custo criado com sucesso.');
    }

    public function edit(CentroCusto $centro_custo)
    {
        return Inertia::render('CostCenters/Edit', [
            'centro' => $centro_custo,
            'centrosPai' => $this->repository->getParentOptions($centro_custo->id),
        ]);
    }

    public function update(CentroCustoRequest $request, CentroCusto $centro_custo)
    {
        $this->service->update($centro_custo, $request->validated());

        return redirect()->route('centros_custo.index')->with('success', 'Centro de custo atualizado com sucesso.');
    }

    public function destroy(CentroCusto $centro_custo)
    {
        $this->service->delete($centro_custo);

        return redirect()->route('centros_custo.index')->with('success', 'Centro de custo removido.');
    }
}
