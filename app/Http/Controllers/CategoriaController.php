<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function Inicio(){
        return view('categorias.categoria');
    }

    public function Index(){
        return view('categorias.index');
    }

   
}
