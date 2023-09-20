<?php
namespace App\Http\Controllers;
use App\Models\Estoque;
use App\Models\Marca;
use App\Models\Produto;
use App\Models\Fornecedor;
use App\Models\Categoria;
use App\Models\MarcaProduto;
use App\Models\CategoriaProduto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    public function index()
    {
        $fornecedores = Fornecedor::all();
        $marcas = Marca::all();
        $categorias = Categoria::all();
        $produtos = Produto::all();
        $estoques = [];
        foreach ($produtos as $produto) {
            $estoquesProduto = $produto->fornecedores->pluck('estoque')->all();
            $estoques = array_merge($estoques, $estoquesProduto); 
        }
        return view('estoque.index',compact('estoques', 'fornecedores', 'marcas', 'categorias','produtos'));
    }

    public function cadastro()
    {
        $produto = Produto::all();
        $marca = Marca::all();
        $fornecedor = Fornecedor::all();
        return view('estoque.cadastro',compact('fornecedor','marca','produto'));
    }

    public function buscar(Request $request){
        $fornecedores = Fornecedor::all();
        $marcas = Marca::all();
        $categorias = Categoria::all();
        $categoriaProduto = CategoriaProduto::all();
        $produtos = Produto::all();
    
        $estoques = [];
        foreach ($produtos as $produto) 
        {   
            $estoquesProduto = $produto->search->pluck('estoque')->all();
            $estoques = array_merge($estoques, $estoquesProduto); 
        }
        return view('estoque.index', compact('estoques', 'produtos','fornecedores','marcas','categorias'));
    }

    public function inserirEstoque(Request $request)
    {
        $fornecedorInput = $request->input('fornecedor');
        $fornecedor = Fornecedor::where('id_fornecedor', $fornecedorInput)->first();
        $marcaInput = $request->input('marca');
        $marca = Marca::where('id_marca', $marcaInput)->first();
        $produtoInput = $request->input('nome_produto');
        $produtoId = Produto::where('id_produto', $produtoInput)->first();

        $estoque = Estoque::create([
            'quantidade'        =>$request->quantidade,
            'localizacao'       =>$request->localizacao,
            'preco_custo'       =>$request->preco_custo,
            'preco_venda'       =>$request->preco_venda,
            'data_chegada'      =>$request->data_chegada,
            'lote'              =>$request->lote,
            'id_produto_fk'     =>$produtoId->id_produto,
            'id_fornecedor_fk'  =>$fornecedor->id_fornecedor,
            'id_marca_fk'       =>$marca->id_marca,
            'quantidade_aviso'  =>$request->quantidade_aviso
        ]);

        MarcaProduto::create([
            'id_produto_fk'     =>$produtoId->id_produto,
            'id_marca_fk'       =>$marca->id_marca
        ]);

        return redirect()->route('estoque.index')->with('success', 'Inserido com sucesso');
    }

    public function editar(Request $request, $estoqueId)
    {
        $produtos = Produto::all();
        $fornecedores = Fornecedor::all();
        $marcas = Marca::all();
        $estoques = Estoque::where('id_estoque', $estoqueId)->get();
        return view('estoque.editar', compact('estoques','produtos','fornecedores','marcas'));
    }

    public function salvarEditar(Request $request, $estoqueId)
    {
        $fornecedorInput = $request->input('fornecedor');
        $fornecedor = Fornecedor::where('id_fornecedor', $fornecedorInput)->first();
        $marcaInput = $request->input('marca');
        $marca = Marca::where('id_marca', $marcaInput)->first();
        $produtoInput = $request->input('nome_produto');
        $produtoId = Produto::where('id_produto', $produtoInput)->first();
        $estoques = Estoque::where('id_estoque' , $estoqueId)
        ->update([
            'quantidade'        =>$request->quantidade,
            'localizacao'       =>$request->localizacao,
            'preco_custo'       =>$request->preco_custo,
            'preco_venda'       =>$request->preco_venda,
            'data_chegada'      =>$request->data_chegada,
            'lote'              =>$request->lote,
            'id_produto_fk'     =>$produtoId->id_produto,
            'id_fornecedor_fk'  =>$fornecedor->id_fornecedor,
            'id_marca_fk'       =>$marca->id_marca,
            'quantidade_aviso'  =>$request->quantidade_aviso
        ]);

        MarcaProduto::where('id_produto_fk', $produtoInput)
        ->update([
            'id_produto_fk' => $produtoId->id_produto,
            'id_marca_fk'   => $marca->id_marca
        ]);

        return redirect()->route('estoque.index');
    }

    public function status($statusId)
    {
        $status = Estoque::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return response()->json(['status' => $status->status]);
    }

    public function quantidade(Request $request){

    }
}
