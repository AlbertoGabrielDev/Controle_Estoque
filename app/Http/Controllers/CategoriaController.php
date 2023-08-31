<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
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
            'categoria' =>$request->categoria
        ]);
    
         return redirect()->route('categoria.index')->with('success', 'Inserido com sucesso');
        // return view('categorias.index',compact('categoria'));
    }

}
