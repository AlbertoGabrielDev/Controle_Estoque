<?php

namespace App\Http\Controllers;

use App\Models\Fornecedor;
use App\Models\Telefone;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use App\Http\Requests\FornecedorStoreRequest;
use App\Http\Requests\FornecedorUpdateRequest;
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
        $validated = $request->validated();

        $fornecedor = Fornecedor::create([
            'codigo' => $validated['codigo'],
            'razao_social' => $validated['razao_social'] ?? null,
            'nome_fornecedor' => $validated['nome_fornecedor'],
            'cnpj' => $validated['cnpj'],
            'nif_cif' => $validated['nif_cif'] ?? null,
            'cep' => $validated['cep'],
            'logradouro' => $validated['logradouro'],
            'bairro' => $validated['bairro'],
            'numero_casa' => $validated['numero_casa'],
            'email' => $validated['email'],
            'id_users_fk' => Auth::id(),
            'cidade' => $validated['cidade'],
            'uf' => strtoupper((string) $validated['uf']),
            'endereco' => $validated['endereco'] ?? null,
            'prazo_entrega_dias' => $validated['prazo_entrega_dias'] ?? 0,
            'condicao_pagamento' => $validated['condicao_pagamento'] ?? null,
            'ativo' => (bool) $validated['ativo'],
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

    public function salvarEditar(FornecedorUpdateRequest $request, $fornecedorId)
    {
        $validated = $request->validated();

        Fornecedor::query()
            ->where('id_fornecedor', $fornecedorId)
            ->update([
                'codigo' => $validated['codigo'],
                'razao_social' => $validated['razao_social'] ?? null,
                'nome_fornecedor' => $validated['nome_fornecedor'],
                'cnpj' => $validated['cnpj'],
                'nif_cif' => $validated['nif_cif'] ?? null,
                'cep' => $validated['cep'],
                'logradouro' => $validated['logradouro'],
                'bairro' => $validated['bairro'],
                'numero_casa' => $validated['numero_casa'],
                'email' => $validated['email'] ?? null,
                'cidade' => $validated['cidade'],
                'uf' => strtoupper((string) $validated['uf']),
                'endereco' => $validated['endereco'] ?? null,
                'prazo_entrega_dias' => $validated['prazo_entrega_dias'] ?? 0,
                'condicao_pagamento' => $validated['condicao_pagamento'] ?? null,
                'ativo' => (bool) $validated['ativo'],
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

    public function destroy($fornecedorId)
    {
        $fornecedor = Fornecedor::query()->findOrFail($fornecedorId);

        if (method_exists($fornecedor, 'produtos') && $fornecedor->produtos()->exists()) {
            return redirect()
                ->route('fornecedor.index')
                ->with('error', 'Não é possível remover: há produtos vinculados a este fornecedor.');
        }

        $fornecedor->delete();

        return redirect()->route('fornecedor.index')->with('success', 'Fornecedor removido.');
    }

    public function getCidade(string $estado)
    {
        return response()->json([]);
    }
}
