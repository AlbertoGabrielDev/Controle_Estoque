<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use App\Http\Requests\ValidacaoUsuario;
use App\Models\Unidades;
use App\Repositories\UsuarioRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{

    protected $usuarioRepository;

    public function __construct(UsuarioRepository $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function Index(){
        $usuarios =  $this->usuarioRepository->index();
        return response()->json($usuarios);
    }

    public function Cadastro(){
        return view('usuario.cadastro')->with('success', 'Usuario inserido com sucesso');
    }

    public function inserirUsuario(Request $request ){
           $usuarios=  $this->usuarioRepository->inserir($request);
            return response()->json($usuarios);
    }

    public function buscar(Request $request)
    {   
        $usuarios = $this->usuarioRepository->buscar($request);
        return response()->json($usuarios);
    }

    public function editar($usuarioId){
        $usuarios = User::where('id' , $usuarioId)->get();
        return view('usuario.editar', compact('usuarios'));  
    }
    
    public function salvarEditar(ValidacaoUsuario $request, $userId)
    {   

        $usuarios = $this->usuarioRepository->salvarEditar($request, $userId);
        return response()->json($usuarios);
    }

    public function status($statusId)
    {
        $status = User::findOrFail($statusId);
        Gate::authorize('permissao');
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return response()->json(['status' => $status->status]);
    }

    public function unidade(){
        $units = Unidades::all();
        return view('auth.login',compact('units'));
    }
    public function unidadeRegister(){
        $units = Unidades::all();
        return view('auth.register',compact('units'));
    }
 }
