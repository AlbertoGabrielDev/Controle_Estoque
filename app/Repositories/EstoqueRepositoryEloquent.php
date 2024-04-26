<?php

namespace App\Repositories;

use App\Http\Requests\ValidacaoEstoque;
use App\Models\Categoria;
use App\Models\Estoque;
use App\Models\Fornecedor;
use App\Models\Marca;
use App\Models\MarcaProduto;
use App\Models\Produto;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\EstoqueRepository;
use Illuminate\Support\Facades\Auth;
use App\Validators\EstoqueValidator;
use Illuminate\Support\Facades\Gate;

/**
 * Class EstoqueRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class EstoqueRepositoryEloquent extends BaseRepository implements EstoqueRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Estoque::class;
    }

    public function index()
    {
        $fornecedores = Fornecedor::all();
        $marcas = Marca::all();
        $categorias = Categoria::all();
        $produtos = Produto::paginate(2);
        if (Gate::allows('permissao')) {
            $estoques = [];
            foreach ($produtos as $produto) 
            {
                $estoquesProduto = $produto->fornecedores->pluck('estoque')->all();
                $estoques = array_merge($estoques, $estoquesProduto); 
            }
        } else {
            $estoques = [];
            foreach ($produtos as $produto) 
            {
                $estoquesProduto = $produto->fornecedores->pluck('estoque')->where('status', 1)->all();
                $estoques = array_merge($estoques, $estoquesProduto); 
            }
        }
        return compact('estoques','produtos','fornecedores','marcas','categorias');
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
            'validade'          =>$request->validade,
            'id_users_fk'       =>Auth::id()
        ]);

      $marcaProduto = MarcaProduto::create([
            'id_produto_fk'     =>$request->input('nome_produto'),
            'id_marca_fk'       =>$request->input('marca')
        ]);
      //  return redirect()->route('estoque.index')->with('success', 'Inserido com sucesso');
      
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
