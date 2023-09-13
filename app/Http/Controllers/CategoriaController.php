<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
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
        $categoria =  Categoria::create([
            'nome_categoria' => $request->categoria,
            'id_users_fk' => Auth::id()
        ]);
        return redirect()->route('categoria.index')->with('success', 'Inserido com sucesso');
    }

}
