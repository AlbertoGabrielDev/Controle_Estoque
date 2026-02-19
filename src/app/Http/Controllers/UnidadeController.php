<?php

namespace App\Http\Controllers;

use App\Models\Unidades;
use App\Repositories\UnidadesRepository;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UnidadeController extends Controller
{
    public function __construct(
        protected UnidadesRepository $unidadeRepository,
        private DataTableService $dt
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

    public function inserirUnidade(Request $request)
    {
        $request->validate([
            'nome' => 'required|max:255',
        ]);

        $this->unidadeRepository->inserirUnidade($request);

        return redirect()->route('unidade.index')->with('success', 'Inserido com sucesso');
    }

    public function editar($unidadeId)
    {
        return Inertia::render('Units/Edit', [
            'unidade' => Unidades::query()->findOrFail($unidadeId),
        ]);
    }

    public function salvarEditar(Request $request, $unidadeId)
    {
        $request->validate([
            'nome' => 'required|max:255',
        ]);

        $this->unidadeRepository->salvarEditar($request, $unidadeId);

        return redirect()->route('unidade.index')->with('success', 'Editado com sucesso');
    }

    public function buscar(Request $request)
    {
        return redirect()->route('unidade.index', [
            'q' => (string) $request->input('nome', ''),
        ]);
    }
}
