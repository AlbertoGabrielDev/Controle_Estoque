<?php
namespace App\Http\Controllers;

use App\Enums\Canal;
use App\Enums\TipoOperacao;
use App\Http\Requests\ValidacaoEstoque;
use App\Models\Produto;
use App\Repositories\EstoqueRepository;
use App\Services\TaxCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstoqueController extends Controller
{
    protected EstoqueRepository $estoqueRepository;
    protected TaxCalculatorService $taxCalculator;

    public function __construct(EstoqueRepository $estoqueRepository, TaxCalculatorService $taxCalculator)
    {
        $this->estoqueRepository = $estoqueRepository;
        $this->taxCalculator = $taxCalculator;
    }
    public function index()
    {
        $estoques = $this->estoqueRepository->index();
        return view('estoque.index', $estoques);
    }

    public function historico()
    {
        $historicos = $this->estoqueRepository->historico();
        return view('estoque.historico', compact('historicos'));
    }

    public function cadastro()
    {
        $cadastro = $this->estoqueRepository->cadastro();
        return view('estoque.cadastro',$cadastro);
        return view('estoque.cadastro', $cadastro);
    }

    public function buscar(Request $request)
    {
        $buscar = $this->estoqueRepository->buscar($request);
        return view('estoque.index',$buscar);
    }

    public function inserirEstoque(ValidacaoEstoque $request)
    {
        $calc = $this->calcularImpostoParaProduto(
            (int) $request->id_produto_fk,
            $request->only('preco_venda'),
            false
        );

        $data = $request->merge([
            'id_users_fk' => Auth::id(),
            'imposto_total' => $calc['_total_impostos'] ?? 0,
            'impostos_json' => json_encode($calc),
        ])->all();

        $this->estoqueRepository->inserirEstoque($data);
        return redirect()->route('estoque.index')->with('success', 'Inserido com sucesso');
    }

    public function editar($estoqueId)
    {
        $editar = $this->estoqueRepository->editar($estoqueId);
        $estoque = $editar['estoque'];

        // Recalcula impostos para mostrar no preview do editar
        $raw = $this->calcularImpostoParaProduto(
            (int) $estoque->id_produto_fk,
            ['preco_venda' => (float) $estoque->preco_venda],
            false
        );

        $previewVM = [
            '__totais' => [
                'preco_base' => (float) $estoque->preco_venda,
                'total_impostos' => $raw['_total_impostos'] ?? 0,
                'total_com_impostos' => $raw['_total_com_impostos'] ?? ((float) $estoque->preco_venda + ($raw['_total_impostos'] ?? 0)),
            ],
            'impostos' => array_values(array_filter(
                $raw,
                fn($v, $k) => is_array($v) && !str_starts_with((string) $k, '_'),
                ARRAY_FILTER_USE_BOTH
            )),
        ];

        return view('estoque.editar', array_merge($editar, [
            'previewVM' => $previewVM,
            'impostos_raw' => $raw,
        ]));
    }


    public function salvarEditar(Request $request, $estoqueId)
    {
        $estoque = Estoque::findOrFail($estoqueId);
        $fillable = $estoque->getFillable();

        $data = $request->only($fillable);

        $produtoId = (int) ($data['id_produto_fk'] ?? $estoque->id_produto_fk);

        $raw = $this->calcularImpostoParaProduto(
            $produtoId,
            ['preco_venda' => (float) ($data['preco_venda'] ?? $estoque->preco_venda ?? 0)],
            false
        );

        $data['impostos_json'] = json_encode($raw);
        $data['imposto_total'] = $raw['_total_impostos'] ?? 0;

        $estoque->fill($data)->save();

        return redirect()->route('estoque.index')->with('success', 'Estoque atualizado com sucesso!');
    }

    public function calcImpostos(Request $req, TaxCalculatorService $svc)
    {
        $produto = Produto::findOrFail($req->integer('id_produto_fk'));
        $valor = (float) $req->input('preco_venda', 0);

        $ctx = array_replace_recursive([
            'ignorar_segmento' => true,
            'operacao' => [
                // defaults coerentes com seus enums
                'tipo' => TipoOperacao::Venda->value,   // 'venda'
                'canal' => Canal::Balcao->value,         // 'balcao'
                'uf_origem' => strtoupper(config('empresa.uf_origem', 'GO')),
                'uf_destino' => strtoupper($req->input('uf_destino', config('empresa.uf_origem', 'GO'))),
            ],
            'produto' => [
                'categoria_id' => $produto->id_categoria ?? null,
                'ncm' => $produto->ncm ?? null,
            ],
            'clientes' => [
                'uf' => strtoupper((string) $req->input('cliente.uf', $req->input('uf_destino', ''))),
            ],
            'valores' => [
                'valor' => $valor,
                'desconto' => 0,
                'frete' => 0,
            ],
        ], (array) $req->input('contexto', []));

        $raw = $svc->calcular($ctx);

        $vm = [
            '__totais' => [
                'preco_base' => $ctx['valores']['valor'],
                'total_impostos' => $raw['_total_impostos'] ?? 0,
                'total_com_impostos' => $raw['_total_com_impostos'] ?? ($ctx['valores']['valor'] + ($raw['_total_impostos'] ?? 0)),
            ],
            // pega apenas os blocos de imposto (ignora chaves que começam com "_")
            'impostos' => array_values(array_filter(
                $raw,
                fn($v, $k) => is_array($v) && !str_starts_with((string) $k, '_'),
                ARRAY_FILTER_USE_BOTH
            )),
        ];

        $html = view('estoque.partials._impostos', ['vm' => $vm])->render();

        return response()->json([
            'html' => $html,
            'meta' => ['total_com_impostos' => $vm['__totais']['total_com_impostos']],
            'raw' => $raw,
        ]);
    }

    private function montarContextoImposto(Produto $produto, array $input, bool $debug = false): array
    {
        return [
            'data' => now()->toDateString(),
            'debug' => $debug,
            'operacao' => [
                'tipo' => $input['tipo'] ?? TipoOperacao::Venda->value, // 'venda'
                'canal' => $input['canal'] ?? Canal::Balcao->value,      // 'balcao'
                'uf_origem' => strtoupper($input['uf_origem'] ?? config('app.uf', 'GO')),
                'uf_destino' => strtoupper($input['uf_destino'] ?? config('app.uf', 'GO')),
            ],
            'ignorar_segmento' => true,
            'produto' => [
                'categoria_id' => $produto->id_categoria ?? null,
                'ncm' => $produto->ncm ?? null,
            ],
            'valores' => [
                'valor' => (float) ($input['preco_venda'] ?? 0),
                'desconto' => (float) ($input['desconto'] ?? 0),
                'frete' => (float) ($input['frete'] ?? 0),
            ],
        ];
    }


    private function calcularImpostoParaProduto(int $produtoId, array $input, bool $debug = false): array
    {
        $produto = Produto::findOrFail(id: $produtoId);
        $contexto = $this->montarContextoImposto($produto, $input, $debug);
        return $this->taxCalculator->calcular($contexto);
    }

    private function prepararPreviewParaView(array $calc, float $precoBase): array
    {
        $fmt = fn($n) => number_format((float) $n, 2, ',', '.');

        $impostos = [];
        foreach (array_filter($calc, 'is_array') as $imp) {
            $linhas = [];
            foreach ($imp['linhas'] ?? [] as $l) {
                $metodoNum = $l['metodo'] ?? null;
                $metodo = $l['metodo_label']
                    ?? ($metodoNum === 1 ? 'Percentual' : ($metodoNum === 2 ? 'Valor fixo' : 'Fórmula'));

                $aliquotaOuFixo = $metodoNum === 2
                    ? 'R$ ' . $fmt($l['valor_fixo'] ?? 0)
                    : number_format((float) ($l['aliquota'] ?? 0), 2, ',', '.') . '%';

                $baseInfo = ($l['base_label'] ?? ($l['base_formula'] ?? '')) . ' — R$ ' . $fmt($l['base'] ?? 0);
                if ($metodoNum === 3 && !empty($l['expression'])) {
                    $baseInfo .= ' | expr: ' . $l['expression'];
                }

                $filtros = array_values(array_filter([
                    !empty($l['uf_origem']) ? 'UF Origem: ' . $l['uf_origem'] : null,
                    !empty($l['uf_destino']) ? 'UF Destino: ' . $l['uf_destino'] : null,
                    !empty($l['canal']) ? 'Canal: ' . $l['canal'] : null,
                    !empty($l['tipo_operacao']) ? 'Op: ' . $l['tipo_operacao'] : null,
                    !empty($l['match']['categoria_produto_id'] ?? null) ? 'CatID: ' . $l['match']['categoria_produto_id'] : null,
                ]));

                $linhas[] = [
                    'rule_id' => $l['rule_id'] ?? null,
                    'cumul' => (bool) ($l['cumulativo'] ?? false),
                    'metodo' => $metodo,
                    'aliqfixo' => $aliquotaOuFixo,
                    'base' => $baseInfo,
                    'valor' => 'R$ ' . $fmt($l['valor'] ?? 0),
                    'filtros' => $filtros ? implode(' • ', $filtros) : '—',
                    'vig' => ($l['vigencia_inicio'] ?? '—') . ' → ' . ($l['vigencia_fim'] ?? '—'),
                ];
            }

            $impostos[] = [
                'codigo' => $imp['imposto'] ?? '',
                'nome' => $imp['tax_nome'] ?? '',
                'total' => 'R$ ' . $fmt($imp['total'] ?? 0),
                'linhas' => $linhas,
            ];
        }

        return [
            'preco_base' => 'R$ ' . $fmt($precoBase),
            'total_impostos' => 'R$ ' . $fmt($calc['_total_impostos'] ?? 0),
            'preco_final' => 'R$ ' . $fmt($calc['_total_com_impostos'] ?? $precoBase),
            'impostos' => $impostos,
        ];
    }

}
