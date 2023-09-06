<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fornecedor;
use App\Models\Estado;
use App\Models\Cidade;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FornecedorController extends Controller
{
    public function Index(){

        
        return view('fornecedor.index');
    }

    public function Cadastro(){
        $estado = Estado::all();
        $cidade = Cidade::all();
        $status = Fornecedor::all();
        return view('fornecedor.cadastro', compact('estado','cidade','status'));
    }

    public function getCidade($estado){
        //$estado = Estado::all();
        $cidade = Cidade::where('id_estado_fk', $estado)->get();
        return response()->json($cidade);
        
    }

    public function inserirCadastro(Request $request){
        // $usuario['id_users_fk'] = Auth::id();
        $fornecedor = $request->validate([
            'status' => 'required|boolean'
        ]);

        $cidadeUf = $request->input('cidades');

        $cidade = Cidade::where('id', $cidadeUf)->first();
        //dd($request->$cidade->id);
       $fornecedor = Fornecedor::create([
            'nome_fornecedor'   =>$request->nome_fornecedor,
            'cnpj'              =>$request->cnpj,
            'cep'               =>$request->cep,
            'logradouro'        =>$request->logradouro,
            'bairro'            =>$request->bairro,
            'numero_casa'       =>$request->numero_casa,
            'telefone'          =>$request->telefone,
            'email'             =>$request->email,
            'id_cidade_fk'      =>$cidade->id,
            'id_users_fk'       =>Auth::id(),
            'status'            =>$request->status                                
       ]);

        
        // $cidadeId = $request->input('cidade');
        // $fornecedor->id_cidade_fk = $cidadeId;

        // $fornecedor->save();

     return redirect()->route('fornecedor.index')->with('success','Inserido com sucesso');
    }
}
