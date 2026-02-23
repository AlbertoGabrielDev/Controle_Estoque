<?php

namespace App\Http\Controllers;

use App\Http\Requests\TabelaPrecoStoreRequest;
use App\Http\Requests\TabelaPrecoUpdateRequest;
use App\Models\Fornecedor;
use App\Models\Item;
use App\Models\Marca;
use App\Models\Produto;
use App\Models\TabelaPreco;
use App\Services\DataTableService;
use App\Services\TabelaPrecoService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TabelaPrecoController extends Controller
{
    public function __construct(
        private DataTableService $dt,
        private TabelaPrecoService $service
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('PriceTables/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'ativo' => (string) $request->query('ativo', ''),
            ],
        ]);
    }

    public function data(Request $request)
    {
        [$query, $columnsMap] = TabelaPreco::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('tabelas_preco.edit', $row->id),
                        DataTableActions::status('tabelas_preco.status', 'tabela_preco', $row->id, (bool) $row->st),
                        DataTableActions::delete('tabelas_preco.destroy', $row->id),
                    ]);
                });
            }
        );
    }

    public function create()
    {
        return Inertia::render('PriceTables/Create', [
            'itens' => Item::query()
                ->select('id', 'sku', 'nome', 'preco_base')
                ->orderBy('nome')
                ->get(),
            'produtos' => Produto::query()
                ->select('id_produto', 'cod_produto', 'nome_produto')
                ->orderBy('nome_produto')
                ->get(),
            'marcas' => Marca::query()
                ->select('id_marca', 'nome_marca')
                ->orderBy('nome_marca')
                ->get(),
            'fornecedores' => Fornecedor::query()
                ->select('id_fornecedor', 'nome_fornecedor')
                ->orderBy('nome_fornecedor')
                ->get(),
        ]);
    }

    public function store(TabelaPrecoStoreRequest $request)
    {
        $data = $request->validated();
        $itens = $data['itens'] ?? [];
        unset($data['itens']);

        $this->service->create($data, $itens);

        return redirect()->route('tabelas_preco.index')->with('success', 'Tabela de preço criada com sucesso.');
    }

    public function edit(TabelaPreco $tabela_preco)
    {
        $tabela_preco->load(['itens', 'produtos']);

        return Inertia::render('PriceTables/Edit', [
            'tabela' => $tabela_preco,
            'itens' => Item::query()
                ->select('id', 'sku', 'nome', 'preco_base')
                ->orderBy('nome')
                ->get(),
            'produtos' => Produto::query()
                ->select('id_produto', 'cod_produto', 'nome_produto')
                ->orderBy('nome_produto')
                ->get(),
            'marcas' => Marca::query()
                ->select('id_marca', 'nome_marca')
                ->orderBy('nome_marca')
                ->get(),
            'fornecedores' => Fornecedor::query()
                ->select('id_fornecedor', 'nome_fornecedor')
                ->orderBy('nome_fornecedor')
                ->get(),
        ]);
    }

    public function update(TabelaPrecoUpdateRequest $request, TabelaPreco $tabela_preco)
    {
        $data = $request->validated();
        $itens = $data['itens'] ?? [];
        unset($data['itens']);

        $this->service->update($tabela_preco, $data, $itens);

        return redirect()->route('tabelas_preco.index')->with('success', 'Tabela de preço atualizada com sucesso.');
    }

    public function destroy(TabelaPreco $tabela_preco)
    {
        $this->service->delete($tabela_preco);

        return redirect()->route('tabelas_preco.index')->with('success', 'Tabela de preço removida.');
    }
}
