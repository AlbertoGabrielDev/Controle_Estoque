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
        return view('categorias.index');
    }

    public function Cadastro(){
        return view('categorias.cadastro');
    }

    public function inserirCategoria(Request $request){

        $categoria =  Categoria::create([
            'nome_categoria'    =>$request->categoria,
            'id_users_fk'       =>Auth::id()
        ]);

        // $categoria = Categoria::all();
        
        //$categoriaProduto = $categoria->id_categoria;
        //dd($categoriaProduto);
        //     CategoriaProduto::create([
        //     'id_categoria_fk'      =>$categoria->id_categoria
        // ]);
       

    return redirect()->route('categoria.index')->with('success', 'Inserido com sucesso');
        // return view('categorias.index',compact('categoria'));
    }

}
