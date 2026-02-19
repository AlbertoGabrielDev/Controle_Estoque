<?php

namespace App\Http\Controllers;

use App\Models\Fornecedor;
use App\Models\Telefone;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FornecedorController extends Controller
{
    public function __construct(
        private DataTableService $dt
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('Suppliers/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'status' => (string) $request->query('status', ''),
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

    public function inserirCadastro(Request $request)
    {
        $validated = $request->validate([
            'nome_fornecedor' => 'required|unique:fornecedores,nome_fornecedor|max:60',
            'cnpj' => 'required|unique:fornecedores,cnpj|max:18|min:14',
            'cep' => 'required|max:10|min:8',
            'logradouro' => 'required|max:50',
            'bairro' => 'required|max:50',
            'numero_casa' => 'required|max:15',
            'email' => 'required|email|max:60',
            'cidade' => 'required|max:50',
            'uf' => 'required|max:2',
            'ddd' => 'required|max:2',
            'telefone' => 'required|max:100',
            'principal' => 'nullable|boolean',
            'whatsapp' => 'nullable|boolean',
            'telegram' => 'nullable|boolean',
        ]);

        $fornecedor = Fornecedor::create([
            'nome_fornecedor' => $validated['nome_fornecedor'],
            'cnpj' => $validated['cnpj'],
            'cep' => $validated['cep'],
            'logradouro' => $validated['logradouro'],
            'bairro' => $validated['bairro'],
            'numero_casa' => $validated['numero_casa'],
            'email' => $validated['email'],
            'id_users_fk' => Auth::id(),
            'cidade' => $validated['cidade'],
            'uf' => strtoupper((string) $validated['uf']),
        ]);

        Telefone::create([
            'ddd' => $validated['ddd'],
            'telefone' => $validated['telefone'],
            'principal' => (int) ($validated['principal'] ?? 0),
            'whatsapp' => (int) ($validated['whatsapp'] ?? 0),
            'telegram' => (int) ($validated['telegram'] ?? 0),
            'id_fornecedor_fk' => $fornecedor->id_fornecedor,
        ]);

        return redirect()->route('fornecedor.index')->with('success', 'Inserido com sucesso');
    }

    public function editar($fornecedorId)
    {
        $fornecedor = Fornecedor::query()->findOrFail($fornecedorId);
        $telefone = Telefone::query()
            ->where('id_fornecedor_fk', $fornecedorId)
            ->orderBy('id_telefone')
            ->first();

        return Inertia::render('Suppliers/Edit', [
            'fornecedor' => $fornecedor,
            'telefone' => $telefone,
        ]);
    }

    public function salvarEditar(Request $request, $fornecedorId)
    {
        $validated = $request->validate([
            'ddd' => 'required|max:2',
            'telefone' => 'required|max:100',
            'principal' => 'nullable|boolean',
            'whatsapp' => 'nullable|boolean',
            'telegram' => 'nullable|boolean',
        ]);

        Telefone::query()->updateOrCreate(
            ['id_fornecedor_fk' => $fornecedorId],
            [
                'ddd' => $validated['ddd'],
                'telefone' => $validated['telefone'],
                'principal' => (int) ($validated['principal'] ?? 0),
                'whatsapp' => (int) ($validated['whatsapp'] ?? 0),
                'telegram' => (int) ($validated['telegram'] ?? 0),
            ]
        );

        return redirect()->route('fornecedor.index')->with('success', 'Editado com sucesso');
    }

    public function getCidade(string $estado)
    {
        return response()->json([]);
    }
}
