<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fornecedor;
use App\Models\Estado;

class FornecedorController extends Controller
{
    public function Index(){

        
        return view('fornecedor.index');
    }

    public function Cadastro(){
        $estado = Estado::all();
        return view('fornecedor.cadastro', compact('estado'));
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

       $estado = Estado::all();

    }
}
