<?php
namespace App\Http\Controllers;

use App\Enums\Canal;
use App\Enums\TipoOperacao;
use App\Http\Requests\ValidacaoEstoque;
use App\Models\Estoque;
use App\Models\Produto;
use App\Repositories\EstoqueRepository;
use App\Services\TaxCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function inserirEstoque(Request $request)
    {
        $produtoId = (int) $request->input('id_produto_fk');
        $produto   = Produto::findOrFail($produtoId);

        $categoriaId = DB::table('categoria_produtos')
            ->where('id_produto_fk', $produtoId)
            ->value('id_categoria_fk'); 

        $precoVenda = (float) $request->input('preco_venda', 0);

        $ctx = [
            'data'             => now()->toDateString(),
            'ignorar_segmento' => true,
            'operacao' => [
                'uf_origem'   => strtoupper(config('empresa.uf_origem', 'GO')),
                'uf_destino'  => strtoupper(config('empresa.uf_origem', 'GO')),
            ],
            'produto' => [
                'categoria_id' => $categoriaId, 
                'ncm'          => $produto->ncm ?? null,
            ],
            'valores' => [
                'valor'    => $precoVenda,
                'desconto' => 0,
                'frete'    => 0,
            ],
        ];
       
        $calc = $this->taxCalculator->calcular($ctx);
       
        $primeiraRegraId = null;
        $json = [];
        foreach ($calc as $bloco) {
            dd($calc);
           
            if (!is_array(value: $bloco) || empty($bloco['linhas'])) continue;
            $primeiraRegraId = $bloco['linhas'][0]['rule_dump']['id'] ?? null;

            $json = $bloco['linhas'];
            if ($primeiraRegraId) break;
        }
        $data = $request->merge([
            'id_users_fk'   => Auth::id(),
            'imposto_total' => $calc['_total_impostos'] ?? 0.0,
            'impostos_json' => json_encode($json, JSON_UNESCAPED_UNICODE),
            'id_tax_fk'     => $primeiraRegraId,
        ])->all();

        $this->estoqueRepository->inserirEstoque($data);

        return redirect()->route('estoque.index')->with('success', 'Inserido com sucesso');
    }

    public function editar($estoqueId)
    {
        $editar  = $this->estoqueRepository->editar($estoqueId);
        $estoque = $editar['estoque'];

        // Resolve categoria via pivot para preview
        $categoriaId = DB::table('categoria_produtos')
            ->where('id_produto_fk', $estoque->id_produto_fk)
            ->value('id_categoria_fk');

        $ctx = [
            'data'             => now()->toDateString(),
            'ignorar_segmento' => true,
            'operacao' => [
                'tipo'        => TipoOperacao::Venda->value,
                'canal'       => Canal::Balcao->value,
                'uf_origem'   => strtoupper(config('empresa.uf_origem', 'GO')),
                'uf_destino'  => strtoupper(config('empresa.uf_origem', 'GO')),
            ],
            'produto' => [
                'categoria_id' => $categoriaId,
                'ncm'          => optional($estoque->produtos)->ncm,
            ],
            'valores' => [
                'valor'    => (float) $estoque->preco_venda,
                'desconto' => 0,
                'frete'    => 0,
            ],
        ];

        $raw = $this->taxCalculator->calcular($ctx);

        $previewVM = [
            '__totais' => [
                'preco_base'        => (float) $estoque->preco_venda,
                'total_impostos'    => $raw['_total_impostos'] ?? 0,
                'total_com_impostos'=> $raw['_total_com_impostos'] ?? ((float) $estoque->preco_venda + ($raw['_total_impostos'] ?? 0)),
            ],
            'impostos' => array_values(array_filter(
                $raw,
                fn($v, $k) => is_array($v) && !str_starts_with((string) $k, '_'),
                ARRAY_FILTER_USE_BOTH
            )),
        ];

        return view('estoque.editar', array_merge($editar, [
            'previewVM'   => $previewVM,
            'impostos_raw'=> $raw,
        ]));
    }

    public function salvarEditar(Request $request, $estoqueId)
    {
        $estoque = Estoque::findOrFail($estoqueId);
        $fillable = $estoque->getFillable();
        $data = $request->only($fillable);

        $produtoId = (int) ($data['id_produto_fk'] ?? $estoque->id_produto_fk);

        $categoriaId = DB::table('categoria_produtos')
            ->where('id_produto_fk', $produtoId)
            ->value('id_categoria_fk');

        $ctx = [
            'data'             => now()->toDateString(),
            'ignorar_segmento' => true,
            'operacao' => [
                'tipo'        => TipoOperacao::Venda->value,
                'canal'       => Canal::Balcao->value,
                'uf_origem'   => strtoupper(config('empresa.uf_origem', 'GO')),
                'uf_destino'  => strtoupper(config('empresa.uf_origem', 'GO')),
            ],
            'produto' => [
                'categoria_id' => $categoriaId,
                'ncm'          => optional($estoque->produtos)->ncm,
            ],
            'valores' => [
                'valor'    => (float) ($data['preco_venda'] ?? $estoque->preco_venda ?? 0),
                'desconto' => 0,
                'frete'    => 0,
            ],
        ];

        $raw = $this->taxCalculator->calcular($ctx);

        // seta json e total
        $data['impostos_json'] = json_encode($raw, JSON_UNESCAPED_UNICODE);
        $data['imposto_total'] = $raw['_total_impostos'] ?? 0;

        // opcional: atualizar id_tax_fk com 1ª regra aplicada
        $primeiraRegraId = null;
        foreach ($raw as $bloco) {
            if (!is_array($bloco) || empty($bloco['linhas'])) continue;
            $primeiraRegraId = $bloco['linhas'][0]['rule_dump']['id'] ?? null;
            if ($primeiraRegraId) break;
        }
        $data['id_tax_fk'] = $primeiraRegraId;

        $estoque->fill($data)->save();

        return redirect()->route('estoque.index')->with('success', 'Estoque atualizado com sucesso!');
    }

    public function calcImpostos(Request $req, TaxCalculatorService $svc)
    {
        $produto = Produto::findOrFail($req->integer('id_produto_fk'));
        $valor   = (float) $req->input('preco_venda', 0);

        $categoriaId = DB::table('categoria_produtos')
            ->where('id_produto_fk', $produto->id_produto)
            ->value('id_categoria_fk');

        $ctx = array_replace_recursive([
            'ignorar_segmento' => true,
            'operacao' => [
                'tipo'       => TipoOperacao::Venda->value,
                'canal'      => Canal::Balcao->value,
                'uf_origem'  => strtoupper(config('empresa.uf_origem', 'GO')),
                'uf_destino' => strtoupper($req->input('uf_destino', config('empresa.uf_origem', 'GO'))),
            ],
            'produto' => [
                'categoria_id' => $categoriaId,
                'ncm'          => $produto->ncm ?? null,
            ],
            'clientes' => [
                'uf' => strtoupper((string) $req->input('cliente.uf', $req->input('uf_destino', ''))),
            ],
            'valores' => [
                'valor'    => $valor,
                'desconto' => 0,
                'frete'    => 0,
            ],
        ], (array) $req->input('contexto', []));

        $raw = $svc->calcular($ctx);

        $vm = [
            '__totais' => [
                'preco_base'         => $ctx['valores']['valor'],
                'total_impostos'     => $raw['_total_impostos'] ?? 0,
                'total_com_impostos' => $raw['_total_com_impostos'] ?? ($ctx['valores']['valor'] + ($raw['_total_impostos'] ?? 0)),
            ],
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
            'raw'  => $raw,
        ]);
    }
}
