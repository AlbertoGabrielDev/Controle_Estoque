<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Estoque;
use App\Models\Marca;
use App\Models\Fornecedor;
use App\Models\Inf_nutri;
use App\Models\Categoria;
use App\Models\CategoriaProduto;
use App\Models\MarcaProduto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class ProdutoController extends Controller
{
    public function Produtos(){
        return view('produtos.produtos');
    }

    public function Index(){
        return view('produtos.index');
    }

    public function Cadastro(Request $request) {

        //$dados= $this->inserirCadastro($request);
        $categoria= Categoria::all();
        $marca= Marca::all();
        $fornecedor = Fornecedor::all();

        return view('produtos.cadastro',compact('categoria','marca','fornecedor'));
    }

    public function inserirCadastro(Request $request){
       
        $produto = Produto::create([
            'nome_produto'      =>$request->nome_produto,
            'cod_produto'       =>$request->cod_produto,
            'descricao'         =>$request->descricao,
            'validade'          =>$request->validade,
            'lote'              =>$request->lote,
            'unidade_medida'    =>$request->unidade_medida,
            'id_users_fk'       =>Auth::id()
        ]);
        
        $fornecedor = Fornecedor::latest('id_fornecedor')->first();
        $marca= Marca::latest('id_marca')->first();
        $produtoId = Produto::latest('id_produto')->first();
        $categoria = Categoria::latest('id_categoria')->first();

        $estoque = Estoque::create([
            'quantidade'        =>$request->quantidade,
            'localizacao'       =>$request->localizacao,
            'preco_custo'       =>$request->preco_custo,
            'preco_venda'       =>$request->preco_venda,
            'data_validade'     =>$request->data_validade,
            'data_chegada'      =>$request->data_chegada,
            'id_produto_fk'     =>$request->id_produto_fk,
            'id_fornecedor_fk'  =>$request->id_fornecedor_fk,
            'lote'              =>$request->lote,
            'id_marca_fk'       =>$request->id_marca_fk,
            'localizacao'       =>$request->localizacao,
            'id_produto_fk'     =>$produtoId->id_produto,
            'id_fornecedor_fk'  =>$fornecedor->id_fornecedor,
            'id_marca_fk'       =>$marca->id_marca
        ]);

        
        
        CategoriaProduto::create([
            'id_categoria_fk'      =>$categoria->id_categoria,
            'id_produto_fk'        =>$produtoId->id_produto        
        ]);

        MarcaProduto::create([
            'id_produto_fk'     =>$produtoId->id_produto,
            'id_marca_fk'       =>$marca->id_marca
        ]);



        // $categoriaId = $request->input('categoria');
        // $produto->id_categoria_fk = $categoriaId;

        // $produto->save();

        return redirect()->route('produtos.index')->with('success', 'Inserido com sucesso');

        //  return view('produtos.index',compact('categoria'));
    }
}
