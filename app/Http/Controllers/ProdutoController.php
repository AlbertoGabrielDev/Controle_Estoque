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
        $produtos = Produto::all();
        return view('produtos.index',compact('produtos'));
    }

    public function Cadastro(Request $request) 
    {
        $categoria= Categoria::all();
        return view('produtos.cadastro',compact('categoria'));
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
        $categoria = Categoria::where('id_categoria', $categoriaId)->first();
       
        CategoriaProduto::create([
            'id_categoria_fk'      =>$categoria->id_categoria,
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
        $categorias = Categoria::all();
        $produtos = Produto::where('produto.id_produto' , $produtoId)->get();
        return view('produtos.editar',compact('produtos', 'categorias'));    
    }

    public function salvarEditar(Request $request, $produtoId)
    {       
      
        $produtos = Produto::where('id_produto',$produtoId)
        ->update([
            'nome_produto'      =>$request->nome_produto,
            'cod_produto'       =>$request->cod_produto,
            'descricao'         =>$request->descricao,
            'validade'          =>$request->validade,
            'unidade_medida'    =>$request->unidade_medida,
        ]);
        return redirect()->route('produtos.index')->with('success', 'Editadocom sucesso');
    }

}
