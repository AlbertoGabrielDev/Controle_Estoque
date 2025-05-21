<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marca;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ValidacaoMarca;
class MarcaController extends Controller
{
    public function index()
    {
        $marcas = Marca::get();
        return view('marca.index', compact('marcas'));
    }

    public function cadastro()
    {   
        return view('marca.cadastro');
    }

    public function buscar(Request $request) 
    {
        if (Gate::allows('permissao')) {
            $marcas = Marca::where('nome_marca', 'like', '%' . $request->input('nome_marca') . '%')->paginate(15);
        } else {
            $marcas = Marca::where('nome_marca', 'like', '%' . $request->input('nome_marca') . '%')->where('status',1)->paginate(15);
        }
        
        return view('marca.index', compact('marcas'));
    } 

    public function editar(Request $request, $marcaId)
    {
        $marcas = Marca::where('id_marca' , $marcaId)->get();
        return view('marca.editar',compact('marcas'));
    }

    public function salvarEditar(ValidacaoMarca $request, $marcaId)
    {
        $marcas = Marca::where('id_marca' , $marcaId)
        ->update([
           'nome_marca' => $request->nome_marca 
        ]);
        return redirect()->route('marca.index')->with('success', 'Editado com sucesso');
    }

    public function inserirMarca(ValidacaoMarca $request)
    {
        $marca = Marca::create([
            'nome_marca' => $request->nome_marca,
            'id_users_fk' => Auth::id()
        ]);
        return redirect()->route('marca.index')->with('success', 'Inserido com sucesso');
    }


}
