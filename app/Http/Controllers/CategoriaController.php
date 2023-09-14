<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Produto;
use App\Models\CategoriaProduto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function inicio()
    {
        $categorias = Categoria::all();
        return view('categorias.categoria',compact('categorias'));
    }

    public function index()
    {
        $categorias = Categoria::all();
        return view('categorias.index',compact('categorias'));
    }

    public function cadastro()
    {
        return view('categorias.cadastro');
    }

    public function inserirCategoria(Request $request)
    {

        if($request->hasFile('imagem') && $request->file('imagem')->isValid()){
            $requestImage = $request->imagem;
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")). "." . $extension;
            $requestImage->move(public_path('img/categorias'), $imageName);
            $categoria =  Categoria::create([
                'nome_categoria' => $request->categoria,
                'id_users_fk' => Auth::id(),
                'imagem' => $imageName
            ]);
        };
        return redirect()->route('categoria.index')->with('success', 'Inserido com sucesso');
    }

    public function produto(Request $request , $categoria)
    {
        //$categorias = Categoria::all();
        $rota = $categoria;    
        $variaveis=  CategoriaProduto::join('produto as p', 'categoria_produto.id_produto_fk', '=' , 'p.id_produto')
        ->join('categoria as c', 'categoria_produto.id_categoria_fk' , '=' , 'c.id_categoria')
        ->where('categoria_produto.id_categoria_fk' ,'=' , $rota)->get();   
        return view('categorias.produto',compact('variaveis'));
    }

}
