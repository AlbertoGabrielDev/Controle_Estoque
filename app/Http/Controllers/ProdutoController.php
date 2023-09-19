<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Estoque;
use App\Models\Marca;
use App\Models\Fornecedor;
use App\Models\Inf_nutri;
use App\Models\Categoria;
use App\Models\CategoriaProduto;
use App\Models\MarcaProduto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class ProdutoController extends Controller
{
    public function Produtos()
    {
        return view('produtos.produtos');
    }

    public function Index()
    {
        $categorias = Categoria::all();
        $produtos = Produto::all();
        return view('produtos.index',compact('produtos','categorias'));
    }

    public function Cadastro(Request $request) 
    {
        $categorias= Categoria::all();
        return view('produtos.cadastro',compact('categorias'));
    }

    public function inserirCadastro(Request $request)
    {
        
        $produto = Produto::create([
            'nome_produto'      =>$request->nome_produto,
            'cod_produto'       =>$request->cod_produto,
            'descricao'         =>$request->descricao,
            'validade'          =>$request->validade,
            'unidade_medida'    =>$request->unidade_medida,
            'id_users_fk'       =>Auth::id()
        ]);

        $produtoId = Produto::latest('id_produto')->first();
        $categoriaId = $request->input('nome_categoria');
        
        CategoriaProduto::create([
            'id_categoria_fk'      =>$categoriaId,
            'id_produto_fk'        =>$produtoId->id_produto        
        ]);
        
        return redirect()->route('produtos.index')->with('success', 'Inserido com sucesso');
    }

    public function buscarProduto(Request $request)
    {
        $buscarProduto= $request->input('nome_produto');
        $produtos = Produto::where('nome_produto', 'like', '%' .$buscarProduto. '%')->get();
        return view('produtos.index', compact('produtos'));
    }

    public function editar(Request $request, $produtoId) 
    {
        // $categorias = Categoria::join('categoria_produto', 'categoria.id_categoria', '=', 'categoria_produto.id_categoria_fk')
        // ->join('produto', 'produto.id_produto', '=', 'categoria_produto.id_produto_fk')
        // ->get();
       // $produto = Produto::find($produtoId);
        $categorias = Categoria::all();
        $produtos = Produto::where('produto.id_produto' , $produtoId)->get();
        return view('produtos.editar',compact('produtos', 'categorias'));    
    }

    public function salvarEditar(Request $request, $produtoId)
    {   
        $categoriaId = $request->input('nome_categoria');
        $produtos = Produto::where('id_produto',$produtoId)
        ->update([
            'nome_produto'      =>$request->nome_produto,
            'cod_produto'       =>$request->cod_produto,
            'descricao'         =>$request->descricao,
            'validade'          =>$request->validade,
            'unidade_medida'    =>$request->unidade_medida,
        ]);
        $categoria = CategoriaProduto::where('id_produto_fk' , $produtoId)
        ->update(['id_categoria_fk' =>$categoriaId]);
        
        return redirect()->route('produtos.index')->with('success', 'Editadocom sucesso');
    }

    public function status(Request $request, $statusId)
    {
        $status = Produto::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return response()->json(['status' => $status->status]);
    }

}
