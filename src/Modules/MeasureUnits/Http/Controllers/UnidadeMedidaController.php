<?php

namespace Modules\MeasureUnits\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\MeasureUnits\Http\Requests\UnidadeMedidaRequest;
use Modules\MeasureUnits\Models\UnidadeMedida;
use Modules\MeasureUnits\Services\UnidadeMedidaService;

class UnidadeMedidaController extends Controller
{
    public function __construct(
        private DataTableService $dt,
        private UnidadeMedidaService $service
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('MeasureUnits/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'ativo' => (string) $request->query('ativo', ''),
            ],
        ]);
    }

    public function data(Request $request)
    {
        [$query, $columnsMap] = UnidadeMedida::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('unidades_medida.edit', $row->id),
                        DataTableActions::status('unidades_medida.status', 'unidade_medida', $row->id, (bool) $row->st),
                        DataTableActions::delete('unidades_medida.destroy', $row->id),
                    ]);
                });
            }
        );
    }

    public function create()
    {
        return Inertia::render('MeasureUnits/Create', [
            'unidadesBase' => UnidadeMedida::query()
                ->select('id', 'codigo', 'descricao')
                ->orderBy('codigo')
                ->get(),
        ]);
    }

    public function store(UnidadeMedidaRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('unidades_medida.index')->with('success', 'Unidade de medida criada com sucesso.');
    }

    public function edit(UnidadeMedida $unidade_medida)
    {
        return Inertia::render('MeasureUnits/Edit', [
            'unidade' => $unidade_medida,
            'unidadesBase' => UnidadeMedida::query()
                ->where('id', '<>', $unidade_medida->id)
                ->select('id', 'codigo', 'descricao')
                ->orderBy('codigo')
                ->get(),
        ]);
    }

    public function update(UnidadeMedidaRequest $request, UnidadeMedida $unidade_medida)
    {
        $this->service->update($unidade_medida, $request->validated());

        return redirect()->route('unidades_medida.index')->with('success', 'Unidade de medida atualizada com sucesso.');
    }

    public function destroy(UnidadeMedida $unidade_medida)
    {
        $this->service->delete($unidade_medida);

        return redirect()->route('unidades_medida.index')->with('success', 'Unidade de medida removida.');
    }
}
