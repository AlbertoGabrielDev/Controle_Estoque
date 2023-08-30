<?php

namespace App\Http\Controllers;

use App\Models\Produto;

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
    //     $evento = [
    //         10,
    //         "Tuts"
    //     ];
    //     $cidade = "Sao Paulo";
    //     $estado = "SP";
    //     $vars_localidade = array("estado","cidade","evento");
    //    //$resultado =compact($vars_localidade);
    //     //dd($resultado);
         $dados= $this->inserirCadastro($request);

//comapct so retorna array
        return view('produtos.cadastro',compact('dados'));
       
    }

    public function inserirCadastro(Request $request){
       

        $produtos = Produto::create([
        'nome_produto'      =>$request->nome_produto,
        'descricao'         =>$request->descricao,
        'validade'          =>$request->validade,
        'lote'              =>$request->lote,
        'unidade_medida'    =>$request->unidade_medida,
        'preco_produto'     =>$request->preco_produto
        ]) ;

            // dd($produtos);
return $produtos;
    }
}
