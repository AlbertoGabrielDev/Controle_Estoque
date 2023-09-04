<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Estoque;
use App\Models\Marca;
use App\Models\Fornecedor;
use App\Models\Inf_nutri;
use App\Models\Categoria;


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

        return view('produtos.cadastro',compact('categoria','marca'));
    }

    public function inserirCadastro(Request $request){
       
        $produto = Produto::create([
            'nome_produto'      =>$request->nome_produto,
            'descricao'         =>$request->descricao,
            'validade'          =>$request->validade,
            'lote'              =>$request->lote,
            'unidade_medida'    =>$request->unidade_medida,
            'preco_produto'     =>$request->preco_produto
        ]);

        // $estoque = Estoque::create([
        //     'quantidade'      =>$request->quantidade,
        //     'localizacao'     =>$request->localizacao,
        //     'data_entrega'    =>$request->data_entrega,
        //     'data_cadastro'   =>$request->data_cadastro,
        // ]);

        // $fornecedor = Fornecedor::create([
        //     'nome_fornecedor'   =>$request->nome_fornecedor,
        //     'preco_fornecedor'  =>$request->preco_fornecedor
        // ]);

        // $marca = Marca::create([
        //     'marca' =>$request->marca
        // ]);

        // $inf_nutri = Inf_nutri::create([
        //     'valor_energetico'  =>$request->valor_energetico,
        //     'carboidrato'       =>$request->carboidrato,
        //     'proteina'          =>$request->proteina,
        //     'sodio'             =>$request->sodio
        // ]);

        $categoria = Categoria::all();

        $categoriaId = $request->input('categoria');
        $produto->id_categoria_fk = $categoriaId;

        $produto->save();
        


        return redirect()->route('produtos.index')->with('success', 'Inserido com sucesso');

        //  return view('produtos.index',compact('categoria'));
    }
}
