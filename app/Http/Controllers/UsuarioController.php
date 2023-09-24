<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Models\User;

class UsuarioController extends Controller
{
    public function Index(){
        $usuarios = User::where('id', '!=', 1)->get();
        return view('usuario.index', compact('usuarios'));
    }

    public function Cadastro(){
        return view('usuario.cadastro');
    }

    public function buscar(Request $request)
    {   
        $usuarios = User::where('name', 'like' , '%' . $request->input('name'). '%')
        ->where('id' , '!=' , 1 )
        ->get();
        return view('usuario.index', compact('usuarios'));
    }

    public function editar(Request $request, $usuarioId){
        $usuarios = User::where('user.id' , $usuarioId)->get();
        return view('usuario.editar');  
    }

    public function status($statusId)
    {
        $status = User::findOrFail($statusId);
        Gate::authorize('permissao');
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return response()->json(['status' => $status->status]);
    }
 }
