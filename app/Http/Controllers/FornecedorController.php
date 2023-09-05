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
        return view('fornecedor.cadastro', compact('estado','cidade'));
    }

    public function getCidade($estado){
        //$estado = Estado::all();
        $cidade = Cidade::where('uf', $estado)->get();
        return response()->json($cidade);
        
    }

    public function inserirCadastro(Request $request){
        // $usuario['id_users_fk'] = Auth::id();
        $cidadeUf = $request->input('cidade');
        dd($cidadeUf);
        $cidade = Cidade::where('id', $cidadeUf)->first();
       $fornecedor = Fornecedor::create([
            'nome_fornecedor'   =>$request->nome_fornecedor,
            'cnpj'              =>$request->cnpj,
            'cep'               =>$request->cep,
            'logradouro'        =>$request->logradouro,
            'bairro'            =>$request->bairro,
            'numero_casa'       =>$request->numero_casa,
            'telefone'          =>$request->telefone,
            'email'             =>$request->email,
            'id_cidade_fk'      =>$request->$cidade->id,
            'id_users_fk'       =>Auth::id()                                
       ]);

        // $cidade = Cidade::all();
        // $cidadeId = $request->input('cidade');
        // $fornecedor->id_cidade_fk = $cidadeId;

        // $fornecedor->save();

     return redirect()->route('fornecedor.index')->with('success','Inserido com sucesso');
    }
}
