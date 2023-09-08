<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Marca;
use App\Models\Produto;
use App\Models\Fornecedor;
use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    public function Index(){
        $estoque = Estoque::all();
        return view('estoque.index',compact('estoque'));
    }

    public function Cadastro(){
        $produto= Produto::all();
        $marca= Marca::all();
        $fornecedor = Fornecedor::all();
        return view('estoque.cadastro',compact('fornecedor','marca','produto'));
    }

    public function inserirEstoque(Request $request){

        $fornecedor = Fornecedor::latest('id_fornecedor')->first();
        $marca= Marca::latest('id_marca')->first();
        $produtoId = Produto::latest('id_produto')->first();
        //dd($request);
        $estoque = Estoque::create([
            'quantidade'        =>$request->quantidade,
            'localizacao'       =>$request->localizacao,
            'preco_custo'       =>$request->preco_custo,
            'preco_venda'       =>$request->preco_venda,
            'data_validade'     =>$request->data_validade,
            'data_chegada'      =>$request->data_chegada,
            'lote'              =>$request->lote,
            'localizacao'       =>$request->localizacao,
            'id_produto_fk'     =>$produtoId->id_produto,
            'id_fornecedor_fk'  =>$fornecedor->id_fornecedor,
            'id_marca_fk'       =>$marca->id_marca
        ]);

        return redirect()->route('estoque.index')->with('success', 'Inserido com sucesso');

    }

}
