<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marca;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MarcaController extends Controller
{
    public function Index(){
        return view('marca.index');
    }

    public function cadastro(){
        return view('marca.cadastro');
    }

    public function inserirMarca(Request $request){
        $marca = Marca::create([
            'nome_marca'     =>$request->nome_marca,
            'id_users_fk'    =>Auth::id()
        ]);

        return redirect()->route('marca.index')->with('success', 'Inserido com sucesso');
    }

}
