<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fornecedor;
use App\Models\Telefone;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FornecedorController extends Controller
{
    public function index()
    {
        $fornecedores = Fornecedor::all();
        return view('fornecedor.index', compact('fornecedores'));
    }

    public function cadastro()
    {
        return view('fornecedor.cadastro');
    }

    public function buscar(Request $request)
    {   
        $buscarFornecedor = $request->input('nome_fornecedor');
        $fornecedores = Fornecedor::where('nome_fornecedor', 'like' , '%' . $buscarFornecedor. '%')->get();
        return view('fornecedor.index', compact('fornecedores'));
    }

    public function inserirCadastro(Request $request)
    {
        $telefones = Telefone::create([
            'ddd' => $request->ddd,
            'telefone' => $request->telefone,
            'principal' => $request->input('principal') ? $request->principal : 0,
            'whatsapp' => $request->input('whatsapp') ? $request->whatsapp : 0,
            'telegram' => $request->input('telegram') ? $request->telegram : 0
        ]);

        $fornecedor = $request->validate([
            'status' => 'required|boolean',
        ]);
        $telefonesId = Telefone::latest('id_telefone')->first();
        $fornecedor = Fornecedor::create([
            'nome_fornecedor'   =>$request->nome_fornecedor,
            'cnpj'              =>$request->cnpj,
            'cep'               =>$request->cep,
            'logradouro'        =>$request->logradouro,
            'bairro'            =>$request->bairro,
            'numero_casa'       =>$request->numero_casa,
            'email'             =>$request->email,
            'id_users_fk'       =>Auth::id(),
            'cidade'            =>$request->cidade,
            'uf'                =>$request->uf,
            'status'            =>$request->status,    
            'id_telefone_fk'    => $telefonesId->id_telefone                           
       ]);
     return redirect()->route('fornecedor.index')->with('success','Inserido com sucesso');
    }

    public function editar(Request $request, $fornecedorId){
        $fornecedores = Fornecedor::where('fornecedor.id_fornecedor' , $fornecedorId)->get();
        $telefones = Telefone::join('fornecedor as f' , 'f.id_telefone_fk' , '=' , 'telefones.id_telefone')
        ->where('f.id_fornecedor', $fornecedorId)->get();
        return view('fornecedor.editar', compact('fornecedores','telefones'));
    }

    public function salvarEditar(Request $request, $fornecedorId) {
        $fornecedores = Fornecedor::where('id_fornecedor' , $fornecedorId)
        ->update([
            'nome_fornecedor'   =>$request->nome_fornecedor,
            'cnpj'              =>$request->cnpj,
            'cep'               =>$request->cep,
            'logradouro'        =>$request->logradouro,
            'bairro'            =>$request->bairro,
            'numero_casa'       =>$request->numero_casa,
            'email'             =>$request->email,
            'cidade'            =>$request->cidade,
            'uf'                =>$request->uf,
            'status'            =>$request->status                     
        ]);
          
        $telefones = Telefone::join('fornecedor as f' , 'f.id_telefone_fk' , '=' , 'telefones.id_telefone')
        ->where('f.id_fornecedor', $fornecedorId)
        ->update([
            'ddd' => $request->ddd,
            'telefone' => $request->telefone,
            'principal' => $request->input('principal') ? $request->principal : 0,
            'whatsapp' => $request->input('whatsapp') ? $request->whatsapp : 0,
            'telegram' => $request->input('telegram') ? $request->telegram : 0
        ]);
        
        return redirect()->route('fornecedor.index')->with('success', 'Editado com sucesso');
    }

    public function status(Request $request, $statusId)
    {
        $status = Fornecedor::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return response()->json(['status' => $status->status]);
    }
}
