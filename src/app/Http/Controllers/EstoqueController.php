<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidacaoEstoque;
use App\Models\Estoque;
use App\Models\Historico;
use App\Services\DataTableService;
use App\Services\EstoqueService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class EstoqueController extends Controller
{
    public function __construct(
        private EstoqueService $estoqueService,
        private DataTableService $dt
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('Stock/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'status' => (string) $request->query('status', ''),
                'cod_produto' => (string) $request->query('cod_produto', ''),
                'nome_produto' => (string) $request->query('nome_produto', ''),
                'nome_fornecedor' => (string) $request->query('nome_fornecedor', ''),
                'nome_marca' => (string) $request->query('nome_marca', ''),
                'lote' => (string) $request->query('lote', ''),
                'localizacao' => (string) $request->query('localizacao', ''),
                'quantidade' => (string) $request->query('quantidade', ''),
                'preco_custo' => (string) $request->query('preco_custo', ''),
                'preco_venda' => (string) $request->query('preco_venda', ''),
                'validade' => (string) $request->query('validade', ''),
                'data_chegada' => (string) $request->query('data_chegada', ''),
                'nome_categoria' => (string) $request->query('nome_categoria', ''),
            ],
        ]);
    }

    public function data(Request $request)
    {
        [$query, $columnsMap] = Estoque::makeDatatableQuery($request);

        $categoria = trim((string) $request->query('nome_categoria', ''));
        if ($categoria !== '') {
            $query->whereExists(function ($sq) use ($categoria) {
                $sq->selectRaw('1')
                    ->from('categoria_produtos as cp')
                    ->join('categorias as c', 'c.id_categoria', '=', 'cp.id_categoria_fk')
                    ->whereColumn('cp.id_produto_fk', 'estoques.id_produto_fk')
                    ->where('c.nome_categoria', 'like', '%' . $categoria . '%');
            });
        }

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('estoque.editar', $row->id),
                        DataTableActions::status('estoque.status', 'estoque', $row->id, (bool) $row->st),
                    ]);
                });
            }
        );
    }

    public function historico(): InertiaResponse
    {
        return Inertia::render('Stock/History', [
            'historicos' => fn () => Historico::query()
                ->with([
                    'estoques.produtos:id_produto,nome_produto',
                    'estoques.marcas:id_marca,nome_marca',
                    'estoques.fornecedores:id_fornecedor,nome_fornecedor',
                ])
                ->orderByDesc('historico_id')
                ->paginate(20)
                ->through(function (Historico $historico): array {
                    $estoque = $historico->estoques;

                    return [
                        'id' => (int) $historico->historico_id,
                        'produto' => (string) optional(optional($estoque)->produtos)->nome_produto,
                        'marca' => (string) optional(optional($estoque)->marcas)->nome_marca,
                        'fornecedor' => (string) optional(optional($estoque)->fornecedores)->nome_fornecedor,
                        'quantidade_retirada' => (int) $historico->quantidade_diminuida,
                        'quantidade' => (int) $historico->quantidade_historico,
                        'venda' => (float) $historico->venda,
                        'data_alteracao' => optional($historico->updated_at)->format('d/m/Y H:i:s'),
                    ];
                }),
        ]);
    }

    public function cadastro()
    {
        $cadastro = $this->estoqueService->cadastroPayload();

        return Inertia::render('Stock/Create', [
            'fornecedores' => $cadastro['fornecedores'] ?? [],
            'marcas' => $cadastro['marcas'] ?? [],
            'produtos' => $cadastro['produtos'] ?? [],
        ]);
    }

    public function buscar(Request $request)
    {
        return redirect()->route('estoque.index', $request->only([
            'q',
            'status',
            'cod_produto',
            'nome_produto',
            'nome_fornecedor',
            'nome_marca',
            'lote',
            'localizacao',
            'quantidade',
            'preco_custo',
            'preco_venda',
            'validade',
            'data_chegada',
            'nome_categoria',
        ]));
    }

    public function inserirEstoque(ValidacaoEstoque $request)
    {
        $this->estoqueService->inserir($request->validated());

        return redirect()->route('estoque.index')->with('success', 'Inserido com sucesso');
    }

    public function editar($estoqueId)
    {
        return Inertia::render('Stock/Edit', $this->estoqueService->editarPayload((int) $estoqueId));
    }

    public function salvarEditar(ValidacaoEstoque $request, $estoqueId)
    {
        $this->estoqueService->salvarEdicao((int) $estoqueId, $request->validated());

        return redirect()->route('estoque.index')->with('success', 'Estoque atualizado com sucesso!');
    }

    public function calcImpostos(Request $request)
    {
        $payload = $this->estoqueService->calcularImpostosPreview($request->all());
        $html = view('estoque.partials._impostos', ['vm' => $payload['vm']])->render();

        return response()->json([
            'vm' => $payload['vm'],
            'html' => $html,
            'meta' => $payload['meta'],
            'raw' => $payload['raw'],
        ]);
    }
}
