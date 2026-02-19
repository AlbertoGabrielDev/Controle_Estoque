<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MarcaController extends Controller
{
    public function __construct(
        private DataTableService $dt
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
            'marca' => Marca::query()->findOrFail($marcaId),
        ]);
    }

    public function salvarEditar(Request $request, $marcaId)
    {
        $marca = Marca::query()->findOrFail($marcaId);
        $validated = $request->validate([
            'nome_marca' => [
                'required',
                'max:20',
                Rule::unique('marcas', 'nome_marca')->ignore($marca->id_marca, 'id_marca'),
            ],
        ]);

        $marca->update([
            'nome_marca' => $validated['nome_marca'],
        ]);

        return redirect()->route('marca.index')->with('success', 'Editado com sucesso');
    }

    public function inserirMarca(Request $request)
    {
        $validated = $request->validate([
            'nome_marca' => 'required|max:20|unique:marcas,nome_marca',
        ]);

        Marca::create([
            'nome_marca' => $validated['nome_marca'],
            'id_users_fk' => Auth::id(),
        ]);

        return redirect()->route('marca.index')->with('success', 'Inserido com sucesso');
    }
}
