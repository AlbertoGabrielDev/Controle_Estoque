<?php
namespace App\Http\Controllers;
use App\Models\Estoque;
use App\Models\Marca;
use App\Models\Produto;
use App\Models\Fornecedor;
use App\Models\Categoria;
use App\Models\MarcaProduto;
use App\Models\Historico;
use App\Models\CategoriaProduto;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\ValidacaoEstoque;
use Illuminate\Support\Facades\Auth;

class EstoqueController extends Controller
{
    public function index()
    {
        $fornecedores = Fornecedor::all();
        $marcas = Marca::all();
        $categorias = Categoria::all();
        $produtos = Produto::paginate(2);
        //$estoques = Estoque::with('produtos')->paginate(5);
        //dd($estoques);
        $estoques = [];
        foreach ($produtos as $produto) 
        {
            $estoquesProduto = $produto->fornecedores->pluck('estoque')->all();
            // dd($estoquesProduto);
            $estoques = array_merge($estoques, $estoquesProduto); 
        }
        return view('estoque.index',compact('estoques','produtos','fornecedores','marcas','categorias'));
    }

    public function historico(){ 
        $historicos = Historico::with('estoques')->get();
        //dd($historicos);
        return view('estoque.historico', compact('historicos'));
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
        $produtos = Produto::paginate(5);
        //$estoques = Estoque::with('produtos')->paginate(2);
        
        // dd($estoques);
        $estoques = [];
        foreach ($produtos as $produto) 
        {   
            $estoquesProduto = $produto->search->pluck('estoque')->all();
            $estoques = array_merge($estoques, $estoquesProduto); 
        }
      
        return view('estoque.index', compact('estoques', 'produtos','fornecedores','marcas','categorias'));
    }

    public function inserirEstoque(ValidacaoEstoque $request)
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
            'quantidade_aviso'  =>$request->quantidade_aviso,
            'id_users_fk'       =>Auth::id()
        ]);

        MarcaProduto::create([
            'id_produto_fk'     =>$request->input('nome_produto'),
            'id_marca_fk'       =>$request->input('marca')
        ]);

        return redirect()->route('estoque.index')->with('success', 'Inserido com sucesso');
    }

    public function editar($estoqueId)
    {
        $produtos = Estoque::find($estoqueId)->produtos->merge(Produto::all());
        $fornecedores = Estoque::find($estoqueId)->fornecedores->merge(Fornecedor::all());
        $marcas = Estoque::find($estoqueId)->marcas->merge(Marca::all());
        $estoques = Estoque::where('id_estoque', $estoqueId)->get();
        return view('estoque.editar', compact('estoques','produtos','fornecedores','marcas'));
    }

    public function salvarEditar(ValidacaoEstoque $request, $estoqueId)
    {
        $estoques = Estoque::where('id_estoque' , $estoqueId)
        ->update([
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

    public function quantidade(Request $request,$estoqueId, $operacao){
        $produto = Estoque::find($estoqueId);
        if ($operacao === 'aumentar') {
            $produto->quantidade += $request->input('quantidadeHistorico');
        } elseif ($operacao === 'diminuir') {
            if ($produto->quantidade > 0) {
                $produto->quantidade -= $request->input('quantidadeHistorico');
                Historico::create([
                    'id_estoque_fk'  =>$estoqueId,    
                    'quantidade_diminuida' =>$request->input('quantidadeHistorico'),
                    'quantidade_historico' =>$produto->quantidade
                ]);
            }
        }
        $produto->save();
        return redirect()->route('estoque.index')->with('success', 'Quantidade atualizada com sucesso');
    }
}
