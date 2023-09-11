<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Marca;
use App\Models\Produto;
use App\Models\Fornecedor;
use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    public function index()
    {
        $estoque = Estoque::join('produto', 'produto.id_produto', '=' , 'estoque.id_produto_fk')
        ->join('marca', 'id_marca', '=' , 'estoque.id_marca_fk')
        ->join('fornecedor', 'fornecedor.id_fornecedor' , '=' , 'estoque.id_fornecedor_fk')
        ->get();
        return view('estoque.index',compact('estoque'));
    }

    public function cadastro()
    {
        $produto= Produto::all();
        $marca= Marca::all();
        $fornecedor = Fornecedor::all();
        return view('estoque.cadastro',compact('fornecedor','marca','produto'));
    }

    public function buscar(Request $request)
    {
        $input = $request->input('inputBusca');
       // dd($input);
        $estoque = Estoque::join('produto as p' , 'p.id_produto' , '=' , 'estoque.id_produto_fk')
        ->join('marca as m', 'm.id_marca', '=', 'estoque.id_marca_fk')
        ->join('marca_produto as mp' , 'mp.id_produto_fk', '=' , 'p.id_produto')
        ->join('marca_produto as mp2' , 'mp2.id_marca_fk' , '=' , 'm.id_marca')
        ->where('p.nome_produto', 'like', '%' . $input . '%')
        ->orWhere('m.nome_marca', 'like', '%' . $input . '%')->get();

        return view('estoque.index', compact('estoque'));
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
