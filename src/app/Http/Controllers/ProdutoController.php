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
        $query = DB::table('produtos as p')
            ->select(
                'p.cod_produto',
                'p.nome_produto',
                DB::raw('COALESCE((SELECT MAX(e.preco_venda) FROM estoques e WHERE e.id_produto_fk = p.id_produto AND e.status = 1), 0) as preco_venda'),
                DB::raw('COALESCE((SELECT SUM(e2.quantidade) FROM estoques e2 WHERE e2.id_produto_fk = p.id_produto AND e2.status = 1), 0) as qtd_disponivel')
            )
            ->where('p.status', 1);
            
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('p.cod_produto', 'like', "%{$q}%")
                    ->orWhere('p.nome_produto', 'like', "%{$q}%");
            });
        }

        return response()->json($query->orderBy('p.nome_produto')->limit(25)->get());
    }

    public function show(string $sku)
    {
        $p = $this->stock->getProductBySku($sku);
        if (!$p)
            return response()->json(['message' => 'Produto não encontrado'], 404);
        return response()->json($p);
    }

}
