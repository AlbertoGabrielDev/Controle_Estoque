<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;

class UsuarioController extends Controller
{
    public function Index(){
        return view('usuario.index');
    }

    public function Cadastro(){
        return view('usuario.cadastro');
    }

    public function inserirUsuario(Request $request){
       

        $produto = Usuario::create([
        'nome_usuario'      =>$request->nome_usuario,
        'login'             =>$request->login,
        'senha'             =>$request->senha
        ]);
        return redirect()->route('usuario.index')->with('success', 'Inserido com sucesso');
        
    }
}
