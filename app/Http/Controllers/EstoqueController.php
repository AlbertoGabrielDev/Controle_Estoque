<?php
namespace App\Http\Controllers;
use App\Models\Estoque;
use App\Models\Marca;
use App\Models\Produto;
use App\Models\Fornecedor;
use App\Models\Categoria;
use App\Models\MarcaProduto;
use App\Models\CategoriaProduto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    public function index()
    {
        $fornecedores = Fornecedor::all();
        $marcas = Marca::all();
        $categorias = Categoria::all();
        // $estoques = Estoque::join('produto', 'produto.id_produto', '=' , 'estoque.id_produto_fk')
        // ->join('marca', 'id_marca', '=' , 'estoque.id_marca_fk')
        // ->join('fornecedor', 'fornecedor.id_fornecedor' , '=' , 'estoque.id_fornecedor_fk')
        // ->get();
        $produtos = Produto::all();
        $estoques = [];
        foreach ($produtos as $produto) {
            $estoquesProduto = $produto->fornecedores->pluck('pivot')->all();
            $estoques = array_merge($estoques, $estoquesProduto);
        }
        //dd($estoques);
       
        return view('estoque.index',compact('estoques', 'fornecedores', 'marcas', 'categorias'));
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
        $categoriaProduto = CategoriaProduto::all();

        $nomeProduto = $request->input('nome_produto');
        $nomeFornecedor = $request->input('nome_fornecedor');
        $nomeMarca = $request->input('nome_marca');
        $nomeCategoria = $request->input('nome_categoria');

        $numeroLote = $request->input('lote');
        $precoCusto = (double)$request->input('preco_custo');
        $precoVenda = (double)$request->input('preco_venda');
        $localizacao = $request->input('localizacao');
        $quantidade = (int)$request->input('quantidade');
        $dataChegada = $request->input('data_chegada');
        //dd($dataChegada);
        $estoque = Estoque::join('produto as p', 'estoque.id_produto_fk', '=', 'p.id_produto')
        ->join('fornecedor as f', 'estoque.id_fornecedor_fk', '=', 'f.id_fornecedor')
        ->join('marca as m', 'estoque.id_marca_fk', '=', 'm.id_marca')
        ->join('categoria_produto as cp', 'cp.id_produto_fk', '=' , 'p.id_produto')
        ->join('categoria as c', 'c.id_categoria' , '=' , 'cp.id_categoria_fk')

        // if ($nomeProduto) $estoque->where('p.nome_produto', 'LIKE', '%' . $nomeProduto . '%')

        // foreach ($array as $a) {
        //     $estoque->where();
        // }
        ->where(function ($query) use ($nomeProduto) {
            $query->where('p.nome_produto', 'LIKE', '%' . $nomeProduto . '%')
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
        ->where(function ($query) use ($nomeCategoria) {
            $query->where('c.nome_categoria', 'like', '%' . $nomeCategoria . '%')
                  ->orWhereNull('c.nome_categoria');
        })
        ->where(function ($query) use ($numeroLote) {
            $query->where('estoque.lote', 'like', '%' . $numeroLote . '%')
                  ->orWhereNull('estoque.lote');
        })
        ->where(function ($query) use ($localizacao) {
            $query->where('estoque.localizacao', 'like', '%' . $localizacao . '%')
                  ->orWhereNull('estoque.localizacao');
        })
        ->where(function ($query) use ($quantidade) {
            $query->where('estoque.quantidade' ,'>=', $quantidade)
                   ->orWhereNull('estoque.quantidade');
        })
        ->where(function ($query) use ($precoVenda) {
            $query->where('estoque.preco_venda', '>=' ,$precoVenda)
                   ->orWhereNull('estoque.preco_venda');
        })
        // ->where(function ($query) use ($dataChegada) {
            
        //     $query->where('estoque.data_chegada' ,'=', $dataChegada)
        //            ->orWhereNull('estoque.data_chegada');
        // })
        ->where(function ($query) use ($precoCusto) {
            $query->where('estoque.preco_custo', '>=' ,$precoCusto)
                   ->orWhereNull('estoque.preco_custo');
        })
        ->get();
        return view('estoque.index', compact('estoque', 'fornecedores', 'marcas', 'categorias'));
    }

    public function inserirEstoque(Request $request)
    {
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
            'id_marca_fk'       =>$marca->id_marca,
            'quantidade_aviso'  =>$request->quantidade_aviso
        ]);

        MarcaProduto::create([
            'id_produto_fk'     =>$produtoId->id_produto,
            'id_marca_fk'       =>$marca->id_marca
        ]);

        return redirect()->route('estoque.index')->with('success', 'Inserido com sucesso');
    }

    public function editar(Request $request, $estoqueId)
    {
        $produtos = Produto::all();
        $fornecedores = Fornecedor::all();
        $marcas = Marca::all();
        $estoques = Estoque::where('id_estoque', $estoqueId)->get();
        return view('estoque.editar', compact('estoques','produtos','fornecedores','marcas'));
    }

    public function salvarEditar(Request $request, $estoqueId)
    {
        $fornecedorInput = $request->input('fornecedor');
        $fornecedor = Fornecedor::where('id_fornecedor', $fornecedorInput)->first();
        $marcaInput = $request->input('marca');
        $marca = Marca::where('id_marca', $marcaInput)->first();
        $produtoInput = $request->input('nome_produto');
        $produtoId = Produto::where('id_produto', $produtoInput)->first();
        $estoques = Estoque::where('id_estoque' , $estoqueId)
        ->update([
            'quantidade'        =>$request->quantidade,
            'localizacao'       =>$request->localizacao,
            'preco_custo'       =>$request->preco_custo,
            'preco_venda'       =>$request->preco_venda,
            'data_chegada'      =>$request->data_chegada,
            'lote'              =>$request->lote,
            'id_produto_fk'     =>$produtoId->id_produto,
            'id_fornecedor_fk'  =>$fornecedor->id_fornecedor,
            'id_marca_fk'       =>$marca->id_marca,
            'quantidade_aviso'  =>$request->quantidade_aviso
        ]);

        MarcaProduto::where('id_produto_fk', $produtoInput)
        ->update([
            'id_produto_fk' => $produtoId->id_produto,
            'id_marca_fk'   => $marca->id_marca
        ]);

        return redirect()->route('estoque.index');
    }

    public function status($statusId)
    {
        $status = Estoque::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return response()->json(['status' => $status->status]);
    }

    public function quantidade(Request $request){

    }
}
