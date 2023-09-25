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
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    public function index()
    {
        $fornecedores = Fornecedor::all();
        $marcas = Marca::all();
        $categorias = Categoria::all();
        $produtos = Produto::paginate(3);
        $estoques = [];
        foreach ($produtos as $produto) 
        {
            $estoquesProduto = $produto->fornecedores->pluck('estoque')->all();
            $estoques = array_merge($estoques, $estoquesProduto); 
        }
      
        return view('estoque.index',compact('estoques','produtos','fornecedores','marcas','categorias'));
    }

    public function cadastro()
    {
        $produto = Produto::all();
        $marca = Marca::all();
        $fornecedores = Fornecedor::all();
        return view('estoque.cadastro',compact('fornecedores','marca','produto'));
    }

    public function buscar(Request $request){
        $fornecedores = Fornecedor::all();
        $marcas = Marca::all();
        $categorias = Categoria::all();
        $produtos = Produto::paginate(10);
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
        $estoque = Estoque::create([
            'quantidade'        =>$request->quantidade,
            'localizacao'       =>$request->localizacao,
            'preco_custo'       =>$request->preco_custo,
            'preco_venda'       =>$request->preco_venda,
            'data_chegada'      =>$request->data_chegada,
            'lote'              =>$request->lote,
            'id_produto_fk'     =>$request->input('nome_produto'),
            'id_fornecedor_fk'  =>$request->input('fornecedor'),
            'id_marca_fk'       =>$request->input('marca'),
            'quantidade_aviso'  =>$request->quantidade_aviso
        ]);

        MarcaProduto::create([
            'id_produto_fk'     =>$request->input('nome_produto'),
            'id_marca_fk'       =>$request->input('marca')
        ]);

        return redirect()->route('estoque.index')->with('success', 'Inserido com sucesso');
    }

    public function editar($estoqueId)
    {
        $produtos = Produto::all();
        $fornecedores = Fornecedor::all();
        $marcas = Marca::all();
        $estoques = Estoque::where('id_estoque', $estoqueId)->get();
        return view('estoque.editar', compact('estoques','produtos','fornecedores','marcas'));
    }

    public function salvarEditar(Request $request, $estoqueId)
    {

        $estoques = Estoque::where('id_estoque' , $estoqueId)
        ->update([
            'quantidade'        =>$request->quantidade,
            'localizacao'       =>$request->localizacao,
            'preco_custo'       =>$request->preco_custo,
            'preco_venda'       =>$request->preco_venda,
            'data_chegada'      =>$request->data_chegada,
            'lote'              =>$request->lote,
            'id_produto_fk'     =>$request->input('nome_produto'),
            'id_fornecedor_fk'  =>$request->input('fornecedor'),
            'id_marca_fk'       =>$request->input('marca'),
            'quantidade_aviso'  =>$request->quantidade_aviso
        ]);

        MarcaProduto::where('id_produto_fk', $request->input('nome_produto'))
        ->update([
            'id_produto_fk' => $request->input('nome_produto'),
            'id_marca_fk'   => $request->input('marca')
        ]);

        return redirect()->route('estoque.index')->with('success', 'Editado com sucesso');
    }

    public function status($statusId)
    {
        $status = Estoque::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return response()->json(['status' => $status->status]);
    }

    public function quantidade($quantidadeId, $operacao){
        $produto = Estoque::find($quantidadeId);

        if ($operacao === 'aumentar') {
            $produto->quantidade += 1;
        } elseif ($operacao === 'diminuir') {
            if ($produto->quantidade > 0) {
                $produto->quantidade -= 1;
            }
        }
        $produto->save();
        return redirect()->route('estoque.index')->with('success', 'Quantidade atualizada com sucesso');
    }
}
