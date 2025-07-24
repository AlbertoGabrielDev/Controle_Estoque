<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Models\Fornecedor;
use App\Models\Telefone;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ValidacaoFornecedor;

class FornecedorController extends Controller
{
    public function index()
    {
        $fornecedores = Fornecedor::paginate(15);
        return view('fornecedor.index', compact('fornecedores'));
    }

    public function cadastro()
    {
        return view('fornecedor.cadastro');
    }

    public function buscar(Request $request)
    {    
        if (Gate::allows('permissao')) {
            $fornecedores = Fornecedor::where('nome_fornecedor', 'like' , '%' . $request->input('nome_fornecedor'). '%')->paginate(15);
        } else {
            $fornecedores = Fornecedor::where('nome_fornecedor', 'like' , '%' . $request->input('nome_fornecedor'). '%')->where('status',1)->paginate(15);
        }
        return view('fornecedor.index', compact('fornecedores'));
    }

    public function inserirCadastro(ValidacaoFornecedor $request)
    {
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
            'uf'                =>$request->uf
       ]);
       $fornecedorId = Fornecedor::latest('id_fornecedor')->first();
        $telefones = Telefone::create([
            'ddd' => $request->ddd,
            'telefone' => $request->telefone,
            'principal' => $request->input('principal') ? $request->principal : 0,
            'whatsapp' => $request->input('whatsapp') ? $request->whatsapp : 0,
            'telegram' => $request->input('telegram') ? $request->telegram : 0,
            'id_fornecedor_fk' => $fornecedorId->id_fornecedor
        ]);
       
       
     return redirect()->route('fornecedor.index')->with('success','Inserido com sucesso');
    }

    public function editar(Request $request, $fornecedorId){
        $fornecedores = Fornecedor::where('fornecedor.id_fornecedor',$fornecedorId)->get();
        $telefones = Fornecedor::find($fornecedorId)->telefones;
        return view('fornecedor.editar', compact('fornecedores','telefones'));
    }

    public function salvarEditar(Request $request, $fornecedorId) {
        Telefone::where('id_fornecedor_fk', $fornecedorId)
        ->update([
            'ddd' => $request->ddd,
            'telefone' => $request->telefone,
            'principal' => $request->input('principal') ? $request->principal : 0,
            'whatsapp' => $request->input('whatsapp') ? $request->whatsapp : 0,
            'telegram' => $request->input('telegram') ? $request->telegram : 0
        ]);
        
        return redirect()->route('fornecedor.index')->with('success', 'Editado com sucesso');
    }

}
