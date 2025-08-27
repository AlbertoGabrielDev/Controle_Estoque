<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ValidacaoProduto;
use App\Http\Requests\ValidacaoProdutoEditar;
use App\Repositories\ProdutoRepository;
use Illuminate\Support\Facades\DB;
use App\Services\StockService;

class ProdutoController extends Controller
{
    protected $produtoRepository;
    protected $stock;
    public function __construct(ProdutoRepository $produtoRepository, StockService $stock)
    {
        $this->produtoRepository = $produtoRepository;
        $this->stock = $stock;
    }
    public function index()
    {
        return view('produtos.index');
    }

    public function data()
    {
        return $this->produtoRepository->getData();
    }

    public function cadastro()
    {
        $categorias = $this->produtoRepository->Cadastro();
        return view('produtos.cadastro', compact('categorias'));
    }

    public function inserirCadastro(ValidacaoProduto $request)
    {
        $this->produtoRepository->inserirCadastro($request);
        return redirect()->route('produtos.index')->with('success', 'Inserido com sucesso');
    }

    public function buscarProduto(Request $request)
    {
        $produtos = $this->produtoRepository->buscar($request);
        return view('produtos.index', compact('produtos'));
    }

    public function editar($produtoId)
    {
        $produtos = $this->produtoRepository->editarView($produtoId);

        return view('produtos.editar', compact('produtos'));
    }

    public function salvarEditar(ValidacaoProdutoEditar $request, $produtoId)
    {
        $this->produtoRepository->update($request, $produtoId);
        return redirect()->route('produtos.index')->with('success', 'Editado com sucesso');
    }

   public function search(Request $request)
{
    $q = trim((string) $request->query('q', ''));

    // Agregação de estoque ativo
    $stockAgg = DB::table('estoques')
        ->select(
            'id_produto_fk',
            DB::raw('MAX(preco_venda)  AS preco_venda'),
            DB::raw('SUM(quantidade)  AS qtd_disponivel')
        )
        ->where('status', 1)
        ->groupBy('id_produto_fk');

    $query = DB::table('produtos as p')
        ->leftJoinSub($stockAgg, 's', fn ($j) => $j->on('s.id_produto_fk', '=', 'p.id_produto'))
        ->where('p.status', 1)
        ->select(
            'p.cod_produto',
            'p.nome_produto',
            DB::raw('COALESCE(s.preco_venda, 0)   AS preco_venda'),
            DB::raw('COALESCE(s.qtd_disponivel, 0) AS qtd_disponivel')
        );

    if ($q !== '') {
        // Normaliza e quebra em termos (ignora termos de 1 char)
        $needle = preg_replace('/\s+/u', ' ', mb_strtolower($q, 'UTF-8'));
        $terms  = array_values(array_filter(
            preg_split('/\s+/u', $needle, -1, PREG_SPLIT_NO_EMPTY),
            fn ($t) => mb_strlen($t, 'UTF-8') > 1
        ));

        // Coloque aqui o collation do seu MySQL/MariaDB que seja case/acento-insensível
        // (ajuste se o seu banco usar outro; utf8mb4_unicode_ci é uma boa base)
        $collation = 'utf8mb4_unicode_ci';

        // 1) Filtro: todos os termos precisam aparecer (AND),
        //    em cod_produto OU nome_produto (OR).
        $query->where(function ($w) use ($terms, $collation) {
            foreach ($terms as $t) {
                $w->where(function ($w2) use ($t, $collation) {
                    $w2->whereRaw("p.cod_produto   COLLATE {$collation} LIKE ?", ["%{$t}%"])
                       ->orWhereRaw("p.nome_produto COLLATE {$collation} LIKE ?", ["%{$t}%"]);
                });
            }
        });

        // 2) Ordenação por "relevância" simples:
        //    - frase exata no nome (mais forte)
        //    - começa com a frase
        //    - contém a frase
        //    - contém na SKU
        $orderSql = "CASE
            WHEN p.nome_produto COLLATE {$collation} = ?  THEN 400
            WHEN p.nome_produto COLLATE {$collation} LIKE ? THEN 300   -- começa com
            WHEN p.nome_produto COLLATE {$collation} LIKE ? THEN 200   -- contém
            WHEN p.cod_produto   COLLATE {$collation} LIKE ? THEN 150  -- SKU contém
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

    // Retorno
    $rows = $query->limit(25)->get()->map(function ($r) {
        $r->preco_venda    = (float) $r->preco_venda;
        $r->qtd_disponivel = (int)   $r->qtd_disponivel;
        return $r;
    });

    return response()->json($rows);
}

    public function show(string $sku)
    {
        $p = $this->stock->getProductBySku($sku);
        if (!$p)
            return response()->json(['message' => 'Produto não encontrado'], 404);
        return response()->json($p);
    }

}
