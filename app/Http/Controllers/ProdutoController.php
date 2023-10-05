<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Support\Facades\Gate;
use App\Models\Categoria;
use App\Models\CategoriaProduto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use App\Http\Requests\ValidacaoProduto;
use App\Http\Requests\ValidacaoProdutoEditar;
use Illuminate\Support\Facades\Validator;

class ProdutoController extends Controller
{
    public function Index()
    {
        $produtos = Gate::allows('permissao') ? Produto::paginate(2) : Produto::where('status', 1)->paginate(2);
        return view('produtos.index', compact('produtos'));
    }

    public function Cadastro(Request $request) 
    {
        $categorias = Categoria::all();
        return view('produtos.cadastro',compact('categorias'));
    }

    public function inserirCadastro(ValidacaoProduto $request)
    {
        $produto = Produto::create([
            'nome_produto'      =>$request->nome_produto,
            'cod_produto'       =>$request->cod_produto,
            'descricao'         =>$request->descricao,
            'validade'          =>$request->validade,
            'unidade_medida'    =>$request->unidade_medida,
            'inf_nutrientes'    =>json_encode($request->inf_nutrientes),
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
        if (Gate::allows('permissao')) {
            $produtos = Produto::where('nome_produto', 'like', '%' .$request->input('nome_produto'). '%')->paginate(5);
        } else {
            $produtos = Produto::where('nome_produto', 'like', '%' .$request->input('nome_produto'). '%')->where('status',1)->paginate(5);
        }
        
        return view('produtos.index', compact('produtos'));
    }

    public function editar($produtoId) 
    {
        $categoria = Categoria::all();
        $categorias = Produto::find($produtoId)->categorias->merge($categoria);
        $produtos = Produto::where('id_produto',$produtoId)->get();
        return view('produtos.editar',compact('produtos', 'categorias'));    
    }

    public function salvarEditar(ValidacaoProdutoEditar $request, $produtoId)
    {   
        $produtos = Produto::where('id_produto',$produtoId)
        ->update([
            'nome_produto'      =>$request->nome_produto,
            'cod_produto'       =>$request->cod_produto,
            'descricao'         =>$request->descricao,
            'validade'          =>$request->validade,
            'unidade_medida'    =>$request->unidade_medida,
            'inf_nutrientes'    =>json_encode($request->inf_nutrientes)
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
