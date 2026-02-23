<?php

namespace App\Http\Controllers;

use App\Enums\Canal;
use App\Enums\TipoOperacao;
use App\Http\Requests\ValidacaoEstoque;
use App\Models\Estoque;
use App\Models\Historico;
use App\Models\Produto;
use App\Repositories\EstoqueRepository;
use App\Services\DataTableService;
use App\Services\TaxCalculatorService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class EstoqueController extends Controller
{
    public function __construct(
        protected EstoqueRepository $estoqueRepository,
        protected TaxCalculatorService $taxCalculator,
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
        $cadastro = $this->estoqueRepository->cadastro();

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
        $validated = $request->validated();
        if (empty($validated['qrcode'])) {
            $validated['qrcode'] = (string) Str::uuid();
        }
        $produtoId = (int) $validated['id_produto_fk'];
        $produto = Produto::findOrFail($produtoId);
        $categoriaId = DB::table('categoria_produtos')
            ->where('id_produto_fk', $produtoId)
            ->value('id_categoria_fk');

        $ctx = $this->buildTaxContext(
            produtoId: $produtoId,
            categoriaId: $categoriaId,
            ncm: $produto->ncm ?? null,
            precoVenda: (float) ($validated['preco_venda'] ?? 0)
        );

        $raw = $this->taxCalculator->calcular($ctx);
        $ruleId = $this->extractRuleId($raw);

        $data = array_merge($validated, [
            'id_users_fk' => Auth::id(),
            'imposto_total' => $raw['_total_impostos'] ?? 0.0,
            'impostos_json' => json_encode($raw['_compact'] ?? $raw, JSON_UNESCAPED_UNICODE),
            'id_tax_fk' => $ruleId,
        ]);

        $this->estoqueRepository->inserirEstoque($data);

        return redirect()->route('estoque.index')->with('success', 'Inserido com sucesso');
    }

    public function editar($estoqueId)
    {
        $editar = $this->estoqueRepository->editar($estoqueId);
        $estoque = $editar['estoque'];
        $categoriaId = DB::table('categoria_produtos')
            ->where('id_produto_fk', $estoque->id_produto_fk)
            ->value('id_categoria_fk');

        $ctx = $this->buildTaxContext(
            produtoId: (int) $estoque->id_produto_fk,
            categoriaId: $categoriaId,
            ncm: optional($estoque->produtos)->ncm,
            precoVenda: (float) ($estoque->preco_venda ?? 0)
        );

        $raw = $this->taxCalculator->calcular($ctx);
        $previewVm = $this->buildPreviewVm($ctx['valores']['valor'], $raw);

        return Inertia::render('Stock/Edit', [
            'estoque' => $estoque,
            'fornecedores' => $editar['fornecedores'] ?? [],
            'marcas' => $editar['marcas'] ?? [],
            'produtos' => $editar['produtos'] ?? [],
            'previewVm' => $previewVm,
            'rawImpostos' => $raw,
        ]);
    }

    public function salvarEditar(ValidacaoEstoque $request, $estoqueId)
    {
        $estoque = Estoque::findOrFail($estoqueId);
        $validated = $request->validated();
        if (array_key_exists('qrcode', $validated) && ($validated['qrcode'] === null || $validated['qrcode'] === '')) {
            unset($validated['qrcode']);
        }
        $produtoId = (int) ($validated['id_produto_fk'] ?? $estoque->id_produto_fk);
        $produto = Produto::findOrFail($produtoId);

        $categoriaId = DB::table('categoria_produtos')
            ->where('id_produto_fk', $produtoId)
            ->value('id_categoria_fk');

        $ctx = $this->buildTaxContext(
            produtoId: $produtoId,
            categoriaId: $categoriaId,
            ncm: $produto->ncm ?? null,
            precoVenda: (float) ($validated['preco_venda'] ?? $estoque->preco_venda ?? 0)
        );

        $raw = $this->taxCalculator->calcular($ctx);
        $ruleId = $this->extractRuleId($raw);

        $data = array_merge($validated, [
            'impostos_json' => json_encode($raw['_compact'] ?? $raw, JSON_UNESCAPED_UNICODE),
            'imposto_total' => $raw['_total_impostos'] ?? 0,
            'id_tax_fk' => $ruleId,
        ]);

        $estoque->fill($data)->save();

        return redirect()->route('estoque.index')->with('success', 'Estoque atualizado com sucesso!');
    }

    public function calcImpostos(Request $request, TaxCalculatorService $svc)
    {
        $produto = Produto::findOrFail($request->integer('id_produto_fk'));
        $valor = (float) $request->input('preco_venda', 0);
        $categoriaId = DB::table('categoria_produtos')
            ->where('id_produto_fk', $produto->id_produto)
            ->value('id_categoria_fk');

        $ctx = $this->buildTaxContext(
            produtoId: (int) $produto->id_produto,
            categoriaId: $categoriaId,
            ncm: $produto->ncm ?? null,
            precoVenda: $valor,
            ufDestino: (string) $request->input('uf_destino', config('empresa.uf_origem', 'GO'))
        );

        $raw = $svc->calcular($ctx);
        $vm = $this->buildPreviewVm($ctx['valores']['valor'], $raw);
        $html = view('estoque.partials._impostos', ['vm' => $vm])->render();

        return response()->json([
            'vm' => $vm,
            'html' => $html,
            'meta' => [
                'total_com_impostos' => $vm['__totais']['total_com_impostos'],
                'id_tax_fk' => $this->extractRuleId($raw),
            ],
            'raw' => $raw,
        ]);
    }

    private function buildTaxContext(
        int $produtoId,
        int|string|null $categoriaId,
        string|null $ncm,
        float $precoVenda,
        string $ufDestino = ''
    ): array {
        $ufOrigem = strtoupper((string) config('empresa.uf_origem', 'GO'));
        $destino = strtoupper($ufDestino !== '' ? $ufDestino : $ufOrigem);

        return [
            'data' => now()->toDateString(),
            'ignorar_segmento' => true,
            'operacao' => [
                'tipo' => TipoOperacao::Venda->value,
                'canal' => Canal::Balcao->value,
                'uf_origem' => $ufOrigem,
                'uf_destino' => $destino,
            ],
            'escopos' => [1], // Estoque usa apenas regra por item para estimativa de imposto do produto.
            'produto' => [
                'id' => $produtoId,
                'categoria_id' => $categoriaId,
                'ncm' => $ncm,
            ],
            'valores' => [
                'valor' => $precoVenda,
                'desconto' => 0,
                'frete' => 0,
            ],
        ];
    }

    private function buildPreviewVm(float $precoBase, array $raw): array
    {
        return [
            '__totais' => [
                'preco_base' => $raw['_total_sem_impostos'] ?? $precoBase,
                'total_impostos' => $raw['_total_impostos'] ?? 0,
                'total_com_impostos' => $raw['_total_com_impostos'] ?? ($precoBase + ($raw['_total_impostos'] ?? 0)),
            ],
            'impostos' => array_values(array_filter(
                $raw,
                fn($v, $k) => is_array($v) && !str_starts_with((string) $k, '_'),
                ARRAY_FILTER_USE_BOTH
            )),
        ];
    }

    private function extractRuleId(array $raw): int|null
    {
        foreach ($raw as $bloco) {
            if (!is_array($bloco) || empty($bloco['linhas']) || !is_array($bloco['linhas'])) {
                continue;
            }

            foreach ($bloco['linhas'] as $linha) {
                $ruleId = (int) data_get($linha, 'rule_id', data_get($linha, 'rule_dump.id', 0));
                if ($ruleId > 0) {
                    return $ruleId;
                }
            }
        }

        return null;
    }
}
