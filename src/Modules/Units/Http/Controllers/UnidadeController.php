<?php

namespace Modules\Units\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Units\Http\Requests\UnidadeStoreRequest;
use Modules\Units\Http\Requests\UnidadeUpdateRequest;
use Modules\Units\Models\Unidades;
use Modules\Units\Services\UnidadeService;

class UnidadeController extends Controller
{
    public function __construct(
        private DataTableService $dt,
        private UnidadeService $service
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('Units/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'status' => (string) $request->query('status', ''),
            ],
        ]);
    }

    public function data(Request $request)
    {
        [$query, $columnsMap] = Unidades::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('unidades.editar', $row->id),
                        DataTableActions::status('unidades.status', 'unidades', $row->id, (bool) $row->st),
                    ]);
                });
            }
        );
    }

    public function cadastro()
    {
        return Inertia::render('Units/Create');
    }

    public function inserirUnidade(UnidadeStoreRequest $request)
    {
        $this->service->create($request->validated(), auth()->id());

        return redirect()->route('unidade.index')->with('success', 'Inserido com sucesso');
    }

    public function editar($unidadeId)
    {
        return Inertia::render('Units/Edit', [
            'unidade' => $this->service->findOrFail((int) $unidadeId),
        ]);
    }

    public function salvarEditar(UnidadeUpdateRequest $request, $unidadeId)
    {
        $this->service->update((int) $unidadeId, $request->validated());

        return redirect()->route('unidade.index')->with('success', 'Editado com sucesso');
    }

    public function buscar(Request $request)
    {
        return redirect()->route('unidade.index', [
            'q' => (string) $request->input('nome', ''),
        ]);
    }
}
