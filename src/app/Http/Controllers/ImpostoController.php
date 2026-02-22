<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImpostoRequest;
use App\Models\Imposto;
use App\Services\DataTableService;
use App\Services\ImpostoService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ImpostoController extends Controller
{
    public function __construct(
        private DataTableService $dt,
        private ImpostoService $service
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('Impostos/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'ativo' => (string) $request->query('ativo', ''),
            ],
        ]);
    }

    public function data(Request $request)
    {
        [$query, $columnsMap] = Imposto::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('impostos.edit', $row->id),
                        DataTableActions::status('impostos.status', 'imposto', $row->id, (bool) $row->st),
                        DataTableActions::delete('impostos.destroy', $row->id),
                    ]);
                });
            }
        );
    }

    public function create()
    {
        return Inertia::render('Impostos/Create');
    }

    public function store(ImpostoRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('impostos.index')->with('success', 'Imposto criado com sucesso.');
    }

    public function edit(Imposto $imposto)
    {
        return Inertia::render('Impostos/Edit', [
            'imposto' => $imposto,
        ]);
    }

    public function update(ImpostoRequest $request, Imposto $imposto)
    {
        $this->service->update($imposto, $request->validated());

        return redirect()->route('impostos.index')->with('success', 'Imposto atualizado com sucesso.');
    }

    public function destroy(Imposto $imposto)
    {
        $this->service->delete($imposto);

        return redirect()->route('impostos.index')->with('success', 'Imposto removido.');
    }
}
