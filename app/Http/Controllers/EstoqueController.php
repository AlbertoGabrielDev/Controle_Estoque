<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    public function Index(){
        return view('estoque.index');
    }


}
