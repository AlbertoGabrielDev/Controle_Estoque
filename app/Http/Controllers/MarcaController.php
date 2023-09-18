<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marca;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MarcaController extends Controller
{
    public function index()
    {
        $marcas = Marca::all();
        return view('marca.index', compact('marcas'));
    }

    public function cadastro()
    {   
        return view('marca.cadastro');
    }

    public function buscar(Request $request) 
    {
        $buscar = $request->input('nome_marca');
        $marcas = Marca::where('nome_marca', 'like', '%' . $buscar . '%')->get();
        return view('marca.index', compact('marcas'));
    } 

    public function editar(Request $request, $marcaId)
    {
        $marcas = Marca::where('id_marca' , $marcaId)->get();
        //dd($marcas);
        return view('marca.editar',compact('marcas'));
    }

    public function salvarEditar(Request $request, $marcaId){
        $marcas = Marca::where('id_marca' , $marcaId)
        ->update([
           'nome_marca' => $request->nome_marca 
        ]);

        return redirect()->route('marca.index')->with('success', 'Editadocom sucesso');
    }

    public function inserirMarca(Request $request)
    {
        $marca = Marca::create([
            'nome_marca' => $request->nome_marca,
            'id_users_fk' => Auth::id()
        ]);
        return redirect()->route('marca.index')->with('success', 'Inserido com sucesso');
    }

}
