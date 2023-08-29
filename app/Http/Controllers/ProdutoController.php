<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function Produtos(){
        return view('produtos.produtos');
    }

    public function Index(){
        return view('produtos.index');
    }

    public function Cadastro(){
        return view('produtos.cadastro');
    }
}
