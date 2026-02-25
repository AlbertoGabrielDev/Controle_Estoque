<?php

namespace Modules\Brands\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Brands\Http\Requests\ValidacaoMarca;
use Modules\Brands\Http\Requests\ValidacaoMarcaEditar;
use Modules\Brands\Models\Marca;
use Modules\Brands\Services\MarcaService;

class MarcaController extends Controller
{
    public function __construct(
        private DataTableService $dt,
        private MarcaService $service
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('Brands/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'status' => (string) $request->query('status', ''),
            ],
        ]);
    }

    public function data(Request $request)
    {
        [$query, $columnsMap] = Marca::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('marca.editar', $row->id),
                        DataTableActions::status('marca.status', 'marca', $row->id, (bool) $row->st),
                    ]);
                });
            }
        );
    }

    public function cadastro()
    {
        return Inertia::render('Brands/Create');
    }

    public function buscar(Request $request)
    {
        return redirect()->route('marca.index', [
            'q' => (string) $request->input('nome_marca', ''),
        ]);
    }

    public function editar($marcaId)
    {
        return Inertia::render('Brands/Edit', [
            'marca' => $this->service->findOrFail((int) $marcaId),
        ]);
    }

    public function salvarEditar(ValidacaoMarcaEditar $request, $marcaId)
    {
        $this->service->update((int) $marcaId, $request->validated());

        return redirect()->route('marca.index')->with('success', 'Editado com sucesso');
    }

    public function inserirMarca(ValidacaoMarca $request)
    {
        $this->service->create($request->validated(), auth()->id());

        return redirect()->route('marca.index')->with('success', 'Inserido com sucesso');
    }
}
