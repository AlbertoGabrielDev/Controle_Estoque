<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidacaoProduto;
use App\Http\Requests\ValidacaoProdutoEditar;
use App\Models\Categoria;
use App\Models\Produto;
use App\Models\UnidadeMedida;
use App\Models\Item;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProdutoController extends Controller
{
    public function __construct(
        private DataTableService $dt
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('Products/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'status' => (string) $request->query('status', ''),
            ],
        ]);
    }

    public function data(Request $request)
    {
        [$query, $columnsMap] = Produto::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('produtos.editar', $row->id),
                        DataTableActions::status('produto.status', 'produto', $row->id, (bool) $row->st),
                    ], 'end');
                });
            }
        );
    }

    public function cadastro()
    {
        return Inertia::render('Products/Create', [
            'categorias' => Categoria::query()
                ->select('id_categoria', 'nome_categoria')
                ->orderBy('nome_categoria')
                ->get(),
            'unidades' => UnidadeMedida::query()
                ->select('id', 'codigo', 'descricao')
                ->orderBy('codigo')
                ->get(),
            'itens' => Item::query()
                ->select('id', 'sku', 'nome')
                ->orderBy('nome')
                ->get(),
        ]);
    }

    public function inserirCadastro(ValidacaoProduto $request)
    {
        $validated = $request->validated();
        $nutrition = $this->normalizeNutrition($validated['inf_nutriente'] ?? null);
        $unidade = UnidadeMedida::query()->find($validated['unidade_medida_id']);
        $unidadeCodigo = $unidade?->codigo;

        $produto = Produto::create([
            'nome_produto' => $validated['nome_produto'],
            'cod_produto' => $validated['cod_produto'],
            'descricao' => $validated['descricao'],
            'unidade_medida' => $unidadeCodigo,
            'unidade_medida_id' => $validated['unidade_medida_id'],
            'item_id' => $validated['item_id'] ?? null,
            'inf_nutriente' => $nutrition,
            'id_users_fk' => Auth::id(),
        ]);

        $produto->categorias()->sync([$validated['id_categoria_fk']]);

        return redirect()->route('produtos.index')->with('success', 'Inserido com sucesso');
    }

    public function buscarProduto(Request $request)
    {
        return redirect()->route('produtos.index', [
            'q' => (string) $request->input('nome_produto', ''),
        ]);
    }

    public function editar($produtoId)
    {
        $produto = Produto::query()
            ->with('categorias:id_categoria,nome_categoria')
            ->findOrFail($produtoId);

        return Inertia::render('Products/Edit', [
            'produto' => $produto,
            'categorias' => Categoria::query()
                ->select('id_categoria', 'nome_categoria')
                ->orderBy('nome_categoria')
                ->get(),
            'unidades' => UnidadeMedida::query()
                ->select('id', 'codigo', 'descricao')
                ->orderBy('codigo')
                ->get(),
            'itens' => Item::query()
                ->select('id', 'sku', 'nome')
                ->orderBy('nome')
                ->get(),
            'categoriaSelecionada' => optional($produto->categorias->first())->id_categoria,
        ]);
    }

    public function salvarEditar(ValidacaoProdutoEditar $request, $produtoId)
    {
        $produto = Produto::query()->findOrFail($produtoId);
        $validated = $request->validated();
        $nutrition = $this->normalizeNutrition($validated['inf_nutriente'] ?? null);
        $unidade = UnidadeMedida::query()->find($validated['unidade_medida_id']);
        $unidadeCodigo = $unidade?->codigo;

        $produto->update([
            'cod_produto' => $validated['cod_produto'],
            'nome_produto' => $validated['nome_produto'],
            'descricao' => $validated['descricao'],
            'unidade_medida' => $unidadeCodigo,
            'unidade_medida_id' => $validated['unidade_medida_id'],
            'item_id' => $validated['item_id'] ?? null,
            'inf_nutriente' => $nutrition,
        ]);

        $produto->categorias()->sync([$validated['id_categoria_fk']]);

        return redirect()->route('produtos.index')->with('success', 'Editado com sucesso');
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $VendaAgg = DB::table('estoques')
            ->select(
                'id_produto_fk',
                DB::raw('MAX(preco_venda)  AS preco_venda'),
                DB::raw('SUM(quantidade)  AS qtd_disponivel')
            )
            ->where('status', 1)
            ->groupBy('id_produto_fk');

        $query = DB::table('produtos as p')
            ->leftJoinSub($VendaAgg, 's', fn($j) => $j->on('s.id_produto_fk', '=', 'p.id_produto'))
            ->where('p.status', 1)
            ->select(
                'p.cod_produto',
                'p.nome_produto',
                DB::raw('COALESCE(s.preco_venda, 0)   AS preco_venda'),
                DB::raw('COALESCE(s.qtd_disponivel, 0) AS qtd_disponivel')
            );

        if ($q !== '') {
            $needle = preg_replace('/\s+/u', ' ', mb_strtolower($q, 'UTF-8'));
            $terms = array_values(array_filter(
                preg_split('/\s+/u', $needle, -1, PREG_SPLIT_NO_EMPTY),
                fn($t) => mb_strlen($t, 'UTF-8') > 1
            ));
            $collation = 'utf8mb4_unicode_ci';
            $query->where(function ($w) use ($terms, $collation) {
                foreach ($terms as $t) {
                    $w->where(function ($w2) use ($t, $collation) {
                        $w2->whereRaw("p.cod_produto   COLLATE {$collation} LIKE ?", ["%{$t}%"])
                            ->orWhereRaw("p.nome_produto COLLATE {$collation} LIKE ?", ["%{$t}%"]);
                    });
                }
            });
            $orderSql = "CASE
            WHEN p.nome_produto COLLATE {$collation} = ?  THEN 400
            WHEN p.nome_produto COLLATE {$collation} LIKE ? THEN 300
            WHEN p.nome_produto COLLATE {$collation} LIKE ? THEN 200
            WHEN p.cod_produto   COLLATE {$collation} LIKE ? THEN 150
            ELSE 0 END DESC, p.nome_produto ASC";

            $orderBindings = [
                $needle,
                "{$needle}%",
                "%{$needle}%",
                "%{$needle}%",
            ];

            $query->orderByRaw($orderSql, $orderBindings);
        } else {
            $query->orderBy('p.nome_produto');
        }

        $rows = $query->limit(25)->get()->map(function ($r) {
            $r->preco_venda = (float) $r->preco_venda;
            $r->qtd_disponivel = (int) $r->qtd_disponivel;
            return $r;
        });

        return response()->json($rows);
    }

    private function normalizeNutrition(string|null $value): array|null
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return ['texto' => trim($value)];
    }
}
