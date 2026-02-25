<?php

namespace Modules\Suppliers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Suppliers\Http\Requests\FornecedorStoreRequest;
use Modules\Suppliers\Http\Requests\FornecedorUpdateRequest;
use Modules\Suppliers\Models\Fornecedor;
use Modules\Suppliers\Services\FornecedorService;
use RuntimeException;

class FornecedorController extends Controller
{
    public function __construct(
        private DataTableService $dt,
        private FornecedorService $service
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('Suppliers/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'status' => (string) $request->query('status', $request->query('ativo', '')),
            ],
        ]);
    }

    public function data(Request $request)
    {
        [$query, $columnsMap] = Fornecedor::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('fornecedor.editar', $row->id),
                        DataTableActions::status('fornecedor.status', 'fornecedor', $row->id, (bool) $row->st),
                        DataTableActions::delete('fornecedor.destroy', $row->id),
                    ]);
                });
            }
        );
    }

    public function cadastro()
    {
        return Inertia::render('Suppliers/Create');
    }

    public function buscar(Request $request)
    {
        return redirect()->route('fornecedor.index', [
            'q' => (string) $request->input('nome_fornecedor', ''),
        ]);
    }

    public function inserirCadastro(FornecedorStoreRequest $request)
    {
        $this->service->create($request->validated(), auth()->id());

        return redirect()->route('fornecedor.index')->with('success', 'Inserido com sucesso');
    }

    public function editar($fornecedorId)
    {
        return Inertia::render('Suppliers/Edit', [
            'fornecedor' => $this->service->findOrFail((int) $fornecedorId),
            'telefone' => $this->service->findPrimaryPhone((int) $fornecedorId),
        ]);
    }

    public function salvarEditar(FornecedorUpdateRequest $request, $fornecedorId)
    {
        $this->service->update((int) $fornecedorId, $request->validated());

        return redirect()->route('fornecedor.index')->with('success', 'Editado com sucesso');
    }

    public function destroy($fornecedorId)
    {
        try {
            $this->service->delete((int) $fornecedorId);
        } catch (RuntimeException $e) {
            return redirect()->route('fornecedor.index')->with('error', $e->getMessage());
        }

        return redirect()->route('fornecedor.index')->with('success', 'Fornecedor removido.');
    }

    public function getCidade(string $estado)
    {
        return response()->json($this->service->listCitiesByUf($estado));
    }
}
