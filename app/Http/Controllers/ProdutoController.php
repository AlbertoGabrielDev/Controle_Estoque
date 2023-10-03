<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Categoria;
use App\Models\CategoriaProduto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdutoController extends Controller
{
    // public function Produtos()
    // {
    //     return view('produtos.produtos');
    // }

    public function Index()
    {
        $produtos = Produto::paginate(2);
        return view('produtos.index',compact('produtos'));
    }

    public function Cadastro(Request $request) 
    {
        $categorias = Categoria::all();
        return view('produtos.cadastro',compact('categorias'));
    }

    public function inserirCadastro(Request $request)
    {
        $rules = $request->validate([
            'nome_produto' => 'required|unique:produto,nome_produto|max:255',
        ],
        [
            'nome_produto.required' =>'O campo "Nome do produto" é obrigatorio',
            'nome_produto.unique' => 'O nome do produto já está cadastrado',
            'nome_produto.max' => 'Máximo de caracteres excedido'
        ]);
//tirar validação daqui, e criar um componente excluisivo para eles
        $produto = Produto::create([
            'nome_produto'      =>$request->nome_produto,
            'cod_produto'       =>$request->cod_produto,
            'descricao'         =>$request->descricao,
            'validade'          =>$request->validade,
            'unidade_medida'    =>$request->unidade_medida,
            'id_users_fk'       =>Auth::id()
        ]);
       
        $produtoId = Produto::latest('id_produto')->first();
        CategoriaProduto::create([
            'id_categoria_fk'      =>$request->input('nome_categoria'),
            'id_produto_fk'        =>$produtoId->id_produto        
        ]);
        return redirect()->route('produtos.index')->with('success', 'Inserido com sucesso');
    }

    public function buscarProduto(Request $request)
    {
        $buscarProduto= $request->input('nome_produto');
        $produtos = Produto::where('nome_produto', 'like', '%' .$buscarProduto. '%')->paginate(2);
        return view('produtos.index', compact('produtos'));
    }

    public function editar($produtoId) 
    {
        $categoria = Categoria::all();
        $categorias = Produto::find($produtoId)->categorias->merge($categoria);
        $produtos = Produto::where('id_produto',$produtoId)->get();
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
        $categoria = CategoriaProduto::where('id_produto_fk' , $produtoId)
        ->update(['id_categoria_fk' =>$request->input('nome_categoria')]);
        return redirect()->route('produtos.index')->with('success', 'Editado com sucesso');
    }

    public function status($statusId)
    {
        $status = Produto::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return response()->json(['status' => $status->status]);
    }

    
}
