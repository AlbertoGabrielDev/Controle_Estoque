<?php
namespace App\Http\Controllers;

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
        return view('estoque.cadastro', $cadastro);
    }

    public function buscar(Request $request)
    {
        $buscar = $this->estoqueRepository->buscar($request);
        return view('estoque.index', $buscar);
    }

    public function inserirEstoque(ValidacaoEstoque $request)
    {
        $calc = $this->calcularImpostoParaProduto(
            (int) $request->id_produto_fk,
            $request->only('preco_venda', 'frete', 'desconto', 'uf_origem', 'uf_destino', 'tipo', 'canal'),
            false
        );
        $precoComImpostos = (float) ($calc['_total_com_impostos'] ?? (float) $request->preco_venda);
        $data = $request->merge([
            'id_users_fk' => Auth::id(),
            'imposto_total' => $precoComImpostos,
        ])->all();
        $this->estoqueRepository->inserirEstoque($data);
        return redirect()->route('estoque.index')->with('success', 'Inserido com sucesso');
    }
    public function editar($estoqueId)
    {
        $editar = $this->estoqueRepository->editar($estoqueId);
        return view('estoque.editar', $editar);
    }

    public function salvarEditar(ValidacaoEstoque $request, $estoqueId)
    {
        $this->estoqueRepository->salvarEditar($request, $estoqueId);
        return redirect()->route('estoque.index')->with('success', 'Editado com sucesso');
    }

    public function calcImpostos(Request $request)
    {
        $request->validate([
            'id_produto_fk' => 'required|integer',
            'preco_venda' => 'required|numeric|min:0',
            'frete' => 'nullable|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0',
            'uf_origem' => 'nullable|string|size:2',
            'uf_destino' => 'nullable|string|size:2',
        ]);

        $produtoId = (int) $request->id_produto_fk;
        $input = $request->only('preco_venda', 'frete', 'desconto', 'uf_origem', 'uf_destino', 'tipo', 'canal');

        $calc = $this->calcularImpostoParaProduto($produtoId, $input, false);
        $precoBase = (float) ($input['preco_venda'] ?? 0);

        // Monte uma VM específica para o partial (reaproveitando seu método)
        $vm = $this->prepararPreviewParaView($calc, $precoBase);

        // Acrescente um bloco de totais “cru” para data-attributes
        $vm['__totais'] = [
            'preco_base' => $precoBase,
            'total_impostos' => (float) ($calc['_total_impostos'] ?? 0),
            'total_com_impostos' => (float) ($calc['_total_com_impostos'] ?? $precoBase),
        ];

        $html = view('estoque.partials._impostos', ['vm' => $vm])->render();

        return response()->json([
            'html' => $html,
            'meta' => $vm['__totais'],
            'raw' => $calc,
        ]);
    }

    private function montarContextoImposto(Produto $produto, array $input, bool $debug = false): array
    {
        $categoriaId = $produto->categorias()->value('categorias.id_categoria');

        $imposto = [
            'data' => now()->toDateString(),
            'debug' => $debug,
            'operacao' => [
                'tipo' => $input['tipo'] ?? 'venda',
                'canal' => ($input['canal'] ?? 'loja') === 'loja' ? 'Fisica' : ($input['canal'] ?? 'loja'),
                'uf_origem' => strtoupper($input['uf_origem'] ?? config('app.uf', 'GO')),
                'uf_destino' => strtoupper($input['uf_destino'] ?? config('app.uf', 'GO')),
            ],
            'ignorar_segmento' => true,
            'produto' => [
                'categoria_id' => $categoriaId,
                'ncm' => null,
            ],
            'valores' => [
                'valor' => (float) ($input['preco_venda'] ?? 0),
                'desconto' => (float) ($input['desconto'] ?? 0),
                'frete' => (float) ($input['frete'] ?? 0),
            ],
        ];
        return $imposto;

    }

    public function previewImpostos(Request $request)
    {
        $request->validate([
            'id_produto_fk' => 'required|integer',
            'preco_venda' => 'required|numeric|min:0',
            'frete' => 'nullable|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0',
            'uf_origem' => 'nullable|string|size:2',
            'uf_destino' => 'nullable|string|size:2',
        ]);
        $cadastro = $this->estoqueRepository->cadastro();
        $calc = $this->calcularImpostoParaProduto(
            (int) $request->id_produto_fk,
            $request->only('preco_venda', 'frete', 'desconto', 'uf_origem', 'uf_destino', 'tipo', 'canal'),
            false
        );
        $precoBase = (float) $request->input('preco_venda', 0);
        $previewVM = $this->prepararPreviewParaView($calc, $precoBase);
        return view('estoque.cadastro', array_merge($cadastro, [
            'previewCalc' => $calc,
            'previewInput' => $request->all(),
            'previewVM' => $previewVM,
        ]));
    }

    private function calcularImpostoParaProduto(int $produtoId, array $input, bool $debug = false): array
    {
        $produto = Produto::findOrFail($produtoId);
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
