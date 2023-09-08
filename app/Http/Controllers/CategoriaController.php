<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\CategoriaProduto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function Inicio(){
        return view('categorias.categoria');
    }

    public function Index(){
        $categoria = Categoria::all();
        return view('categorias.index',compact('categoria'));
    }

    public function Cadastro(){
        return view('categorias.cadastro');
    }

    public function inserirCategoria(Request $request){

        $categoria =  Categoria::create([
            'nome_categoria'    =>$request->categoria,
            'id_users_fk'       =>Auth::id()
        ]);

       

    return redirect()->route('categoria.index')->with('success', 'Inserido com sucesso');
        // return view('categorias.index',compact('categoria'));
    }

}
