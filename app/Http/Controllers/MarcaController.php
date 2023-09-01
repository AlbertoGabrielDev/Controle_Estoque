<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marca;
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
            'marca'     =>$request->marca
        ]);

        return redirect()->route('marca.index')->with('success', 'Inserido com sucesso');
    }

}
