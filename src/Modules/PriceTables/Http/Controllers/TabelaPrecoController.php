<?php

namespace Modules\PriceTables\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Fornecedor;
use App\Models\Item;
use App\Models\Marca;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Products\Models\Produto;
use Modules\PriceTables\Http\Requests\TabelaPrecoStoreRequest;
use Modules\PriceTables\Http\Requests\TabelaPrecoUpdateRequest;
use Modules\PriceTables\Models\TabelaPreco;
use Modules\PriceTables\Services\TabelaPrecoService;
use Modules\Stock\Models\Estoque;

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
        [$marcasPorProduto, $fornecedoresPorProduto] = $this->resolveProdutoAssociacoes();

        return Inertia::render('PriceTables/Create', [
            'itens' => Item::query()
                ->select('id', 'sku', 'nome', 'preco_base')
                ->orderBy('nome')
                ->get(),
            'produtos' => Produto::query()
                ->select('id_produto', 'cod_produto', 'nome_produto')
                ->orderBy('nome_produto')
                ->get(),
            'marcasPorProduto' => $marcasPorProduto,
            'fornecedoresPorProduto' => $fornecedoresPorProduto,
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
        [$marcasPorProduto, $fornecedoresPorProduto] = $this->resolveProdutoAssociacoes($tabela_preco);

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
            'marcasPorProduto' => $marcasPorProduto,
            'fornecedoresPorProduto' => $fornecedoresPorProduto,
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

    private function resolveProdutoAssociacoes(?TabelaPreco $tabelaPreco = null): array
    {
        $marcasPorProduto = [];
        $fornecedoresPorProduto = [];

        $estoques = Estoque::query()
            ->with([
                'marcas:id_marca,nome_marca',
                'fornecedores:id_fornecedor,nome_fornecedor',
            ])
            ->where('status', 1)
            ->get(['id_produto_fk', 'id_marca_fk', 'id_fornecedor_fk']);

        foreach ($estoques as $estoque) {
            $produtoId = (string) ($estoque->id_produto_fk ?? '');
            if ($produtoId === '') {
                continue;
            }

            if ($estoque->id_marca_fk && $estoque->marcas) {
                $marcaId = (int) $estoque->id_marca_fk;
                $marcasPorProduto[$produtoId][$marcaId] = [
                    'id_marca' => $marcaId,
                    'nome_marca' => (string) $estoque->marcas->nome_marca,
                ];
            }

            if ($estoque->id_fornecedor_fk && $estoque->fornecedores) {
                $fornecedorId = (int) $estoque->id_fornecedor_fk;
                $fornecedoresPorProduto[$produtoId][$fornecedorId] = [
                    'id_fornecedor' => $fornecedorId,
                    'nome_fornecedor' => (string) $estoque->fornecedores->nome_fornecedor,
                ];
            }
        }

        if ($tabelaPreco && ($tabelaPreco->tipo_alvo ?? null) === 'produto') {
            $this->mergeAssociacoesSelecionadasDaTabela($tabelaPreco, $marcasPorProduto, $fornecedoresPorProduto);
        }

        $marcasPorProduto = array_map(static fn ($items) => array_values($items), $marcasPorProduto);
        $fornecedoresPorProduto = array_map(static fn ($items) => array_values($items), $fornecedoresPorProduto);

        return [$marcasPorProduto, $fornecedoresPorProduto];
    }

    private function mergeAssociacoesSelecionadasDaTabela(
        TabelaPreco $tabelaPreco,
        array &$marcasPorProduto,
        array &$fornecedoresPorProduto
    ): void {
        if (!$tabelaPreco->relationLoaded('produtos')) {
            $tabelaPreco->load('produtos');
        }

        $marcaIds = [];
        $fornecedorIds = [];

        foreach ($tabelaPreco->produtos as $produto) {
            $pivot = $produto->pivot;
            if (!$pivot) {
                continue;
            }

            if (!empty($pivot->marca_id)) {
                $marcaIds[] = (int) $pivot->marca_id;
            }

            if (!empty($pivot->fornecedor_id)) {
                $fornecedorIds[] = (int) $pivot->fornecedor_id;
            }
        }

        $marcasById = [];
        if ($marcaIds !== []) {
            $marcasById = Marca::query()
                ->whereIn('id_marca', array_values(array_unique($marcaIds)))
                ->get(['id_marca', 'nome_marca'])
                ->keyBy('id_marca')
                ->all();
        }

        $fornecedoresById = [];
        if ($fornecedorIds !== []) {
            $fornecedoresById = Fornecedor::query()
                ->whereIn('id_fornecedor', array_values(array_unique($fornecedorIds)))
                ->get(['id_fornecedor', 'nome_fornecedor'])
                ->keyBy('id_fornecedor')
                ->all();
        }

        foreach ($tabelaPreco->produtos as $produto) {
            $produtoId = (string) ($produto->id_produto ?? '');
            $pivot = $produto->pivot;

            if ($produtoId === '' || !$pivot) {
                continue;
            }

            $marcaId = (int) ($pivot->marca_id ?? 0);
            if ($marcaId > 0 && !isset($marcasPorProduto[$produtoId][$marcaId])) {
                $nomeMarca = $marcasById[$marcaId]->nome_marca ?? "Marca #{$marcaId}";
                $marcasPorProduto[$produtoId][$marcaId] = [
                    'id_marca' => $marcaId,
                    'nome_marca' => (string) $nomeMarca,
                ];
            }

            $fornecedorId = (int) ($pivot->fornecedor_id ?? 0);
            if ($fornecedorId > 0 && !isset($fornecedoresPorProduto[$produtoId][$fornecedorId])) {
                $nomeFornecedor = $fornecedoresById[$fornecedorId]->nome_fornecedor ?? "Fornecedor #{$fornecedorId}";
                $fornecedoresPorProduto[$produtoId][$fornecedorId] = [
                    'id_fornecedor' => $fornecedorId,
                    'nome_fornecedor' => (string) $nomeFornecedor,
                ];
            }
        }
    }
}
