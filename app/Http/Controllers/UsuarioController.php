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

    public function status(Request $request, $statusId)
    {
        $status = User::findOrFail($statusId);
        Gate::authorize('permissao');
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return response()->json(['status' => $status->status]);
    }
 }
