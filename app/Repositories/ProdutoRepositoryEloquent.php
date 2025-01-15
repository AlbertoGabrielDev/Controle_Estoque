<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ProdutoRepository;
use App\Models\Produto;
use App\Validators\ProdutoValidator;
use App\Http\Requests\ValidacaoProduto;
use App\Models\CategoriaProduto;
use App\Http\Requests\ValidacaoProdutoEditar;
use App\Models\Categoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

/**
 * Class ProdutoRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProdutoRepositoryEloquent extends BaseRepository implements ProdutoRepository
{

    public $fieldSearchable = [
        'nome_produto' => 'like',
        'descricao' => 'like',
    ];

    public function model()
    {
        return Produto::class;
    }

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function __construct(Produto $model)
    {
        $this->model = $model;
    }

    public function getById($id)
    {
        return Produto::findOrFail($id);
    }

    public function getAll(){

        if (Gate::allows('permissao')) {
            $query = Produto::query();
            $this->applyLikeConditions($query, request()->get('searchLike'));
            return $query->paginate(15);
        }

        return Produto::where('status', 1)->paginate(15);
    
    }

    public function cadastro()
    {
        $categorias = Categoria::all();
        return $categorias;
    }

    public function inserirCadastro(ValidacaoProduto $request)
    {
        $produto = Produto::create([
            'nome_produto'      => $request->nome_produto,
            'cod_produto'       => $request->cod_produto,
            'descricao'         => $request->descricao,
            'unidade_medida'    => $request->unidade_medida,
            'inf_nutrientes'    => json_encode($request->inf_nutrientes),
            'id_users_fk'       => Auth::id()
        ]);
        $produtoId = Produto::latest('id_produto')->first();
        CategoriaProduto::create([
            'id_categoria_fk'      => $request->input('nome'),
            'id_produto_fk'        => $produtoId->id_produto
        ]);
    }

    public function editarView($produtoId)
    {
        $produtos = Produto::where('id_produto', $produtoId)->get();
        return $produtos;
    }

    public function editar(ValidacaoProdutoEditar $request, $produtoId)
    {
        $produtos = Produto::where('id_produto', $produtoId)
            ->update([
                'descricao'         => $request->descricao,
                'inf_nutrientes'    => json_encode($request->inf_nutrientes)
            ]);
        return $produtos;
    }

    public function statusInativar($statusId)
    {
        $status = Produto::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return $status;
    }

}
