<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use App\Http\Requests\ValidacaoUsuario;

class UsuarioController extends Controller
{
    public function Index(){
        $usuarios = User::where('id', '!=', 1)->paginate(2);
        return view('usuario.index', compact('usuarios'));
    }

    public function Cadastro(){
        return view('usuario.cadastro')->with('success', 'Usuario inserido com sucesso');
    }

    public function buscar(Request $request)
    {   
        $usuarios = User::where('name', 'like' , '%' . $request->input('name'). '%')
        ->where('id' , '!=' , 1 )
        ->paginate(2);
        return view('usuario.index', compact('usuarios'));
    }

    public function editar($usuarioId){
        $usuarios = User::where('id' , $usuarioId)->get();
        return view('usuario.editar', compact('usuarios'));  
    }
    
    public function salvarEditar(ValidacaoUsuario $request, $userId)
    {   
        $users = User::where('id',$userId)
        ->update([
            'name'  =>$request->name,
            'email' =>$request->email
        ]);
      
        return redirect()->route('usuario.index')->with('success', 'Editado com sucesso');
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
