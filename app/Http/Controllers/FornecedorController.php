<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fornecedor;
use App\Models\Estado;
use App\Models\Cidade;

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
       $fornecedor = Fornecedor::create([
            'nome_fornecedor'   =>$request->nome_fornecedor,
            'cnpj'              =>$request->cnpj,
            'logradouro'        =>$request->logradouro,
            'bairro'            =>$request->bairro,
            'numero_casa'       =>$request->numero_casa,
            'telefone'          =>$request->telefone,
            'email'             =>$request->email
       ]);

       //$estado = Estado::all();

    }
}
