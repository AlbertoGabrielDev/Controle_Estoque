<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marca;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MarcaController extends Controller
{
    public function Index(){
        $marca= Marca::all();
        return view('marca.index',compact('marca'));
    }

    public function cadastro(){   
        return view('marca.cadastro');
    }

    public function Buscar(Request $request)
{
    $termo = $request->input('nome_marca');

    $marca = Marca::where('nome_marca', 'like', '%' . $termo . '%')->get();

    return view('marca.index', compact('marca'));
}

    public function Editar(Request $request, $id){
        $editar = Marca::find($id);

        if (!$editar) {
            echo('Não encontrado');
        }

        $editar->nome_marca = $request->input('nome_marca');

        return view('marca.editar',compact('editar'));
    }

    public function inserirMarca(Request $request){
        $marca = Marca::create([
            'nome_marca'     =>$request->nome_marca,
            'id_users_fk'    =>Auth::id()
        ]);

        return redirect()->route('marca.index')->with('success', 'Inserido com sucesso');
    }

}
