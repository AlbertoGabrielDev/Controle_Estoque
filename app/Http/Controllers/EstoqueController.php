<?php
namespace App\Http\Controllers;
use App\Models\Estoque;
use App\Models\Marca;
use App\Models\Produto;
use App\Models\Fornecedor;
use App\Models\Categoria;
use App\Models\MarcaProduto;
use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    public function index()
    {
        $fornecedores = Fornecedor::all();
        $marcas = Marca::all();
        $categorias = Categoria::all();
        $estoque = Estoque::join('produto', 'produto.id_produto', '=' , 'estoque.id_produto_fk')
        ->join('marca', 'id_marca', '=' , 'estoque.id_marca_fk')
        ->join('fornecedor', 'fornecedor.id_fornecedor' , '=' , 'estoque.id_fornecedor_fk')
        ->get();
        return view('estoque.index',compact('estoque', 'fornecedores', 'marcas', 'categorias'));
    }

    public function cadastro()
    {
        $produto = Produto::all();
        $marca = Marca::all();
        $fornecedor = Fornecedor::all();
        return view('estoque.cadastro',compact('fornecedor','marca','produto'));
    }

    public function buscar(Request $request)
    {
        $fornecedores = Fornecedor::all();
        $marcas = Marca::all();
        $categorias = Categoria::all();

        $nomeProduto = $request->input('nome_produto');
        $nomeFornecedor = $request->input('nome_fornecedor');
        $nomeMarca = $request->input('nome_marca');
        $nomeCategoria = $request->input('nome_categoria');
        $numeroLote = $request->input('lote');
        $precoCusto = $request->input('preco_custo');
        $precoVenda = $request->input('preco_venda');
        $localizacao = $request->input('localizacao');

        $estoque = Estoque::join('produto as p', 'estoque.id_produto_fk', '=', 'p.id_produto')
        ->join('fornecedor as f', 'estoque.id_fornecedor_fk', '=', 'f.id_fornecedor')
        ->join('marca as m', 'estoque.id_marca_fk', '=', 'm.id_marca')
        ->join('marca_produto as mp', 'p.id_produto', '=', 'mp.id_produto_fk')
        ->join('marca_produto as mp2', 'm.id_marca', '=', 'mp2.id_marca_fk')
        ->where(function ($query) use ($nomeProduto) {
            $query->where('p.nome_produto', 'like', '%' . $nomeProduto . '%')
                  ->orWhereNull('p.nome_produto');
        })
        ->where(function ($query) use ($nomeMarca) {
            $query->where('m.nome_marca', 'like', '%' . $nomeMarca . '%')
                  ->orWhereNull('m.nome_marca');
        })
        ->where(function ($query) use ($nomeFornecedor) {
            $query->where('f.nome_fornecedor', 'like', '%' . $nomeFornecedor . '%')
                  ->orWhereNull('f.nome_fornecedor');
        })
        ->get();
        
        return view('estoque.index', compact('estoque', 'fornecedores', 'marcas', 'categorias'));
    }

    public function inserirEstoque(Request $request){
        $fornecedorInput = $request->input('fornecedor');
        $fornecedor = Fornecedor::where('id_fornecedor', $fornecedorInput)->first();
        $marcaInput = $request->input('marca');
        $marca = Marca::where('id_marca', $marcaInput)->first();
        $produtoInput = $request->input('nome_produto');
        $produtoId = Produto::where('id_produto', $produtoInput)->first();

        $estoque = Estoque::create([
            'quantidade'        =>$request->quantidade,
            'localizacao'       =>$request->localizacao,
            'preco_custo'       =>$request->preco_custo,
            'preco_venda'       =>$request->preco_venda,
            'data_chegada'      =>$request->data_chegada,
            'lote'              =>$request->lote,
            'id_produto_fk'     =>$produtoId->id_produto,
            'id_fornecedor_fk'  =>$fornecedor->id_fornecedor,
            'id_marca_fk'       =>$marca->id_marca
        ]);

        MarcaProduto::create([
            'id_produto_fk'     =>$produtoId->id_produto,
            'id_marca_fk'       =>$marca->id_marca
        ]);

        return redirect()->route('estoque.index')->with('success', 'Inserido com sucesso');
    }
}
