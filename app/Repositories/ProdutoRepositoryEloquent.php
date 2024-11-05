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

    protected $fieldSearchable = [
        'nome_produto' => 'like',
        'descricao' => 'like',
        'id_produto'
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
    public function getAll()
{
    $search = request()->get('search');
    $searchLike = request()->get('searchLike'); 

    if (Gate::allows('permissao')) {
        $query = Produto::query();

     
        if ($searchLike) {
            foreach ($this->fieldSearchable as $field => $operator) {
                if ($operator === 'like') {
                    $query->orWhere($field, 'LIKE', '%' . $searchLike . '%');
                }
            }
        }
      
        if ($search) {
            $searchArray = array_filter(explode(';', $search));

            foreach ($searchArray as $condition) {
                if (substr_count($condition, ':') === 2) {
                    [$field, $operator, $value] = explode(':', $condition, 3);

                    if (array_key_exists($field, $this->fieldSearchable)) {
                        if (strtolower($operator) === 'like') {
                            $value = '%' . $value . '%';
                        }
                        $query->where($field, $operator, $value);
                    }
                } elseif (substr_count($condition, ':') === 1) {
                    [$field, $value] = explode(':', $condition);
                    $query->where($field, '=', $value);
                }
            }
        }
       
        $produtos = $query->paginate(15);
    } else {
        $produtos = Produto::where('status', 1)->paginate(15);
    }

    return $produtos;
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
            'id_categoria_fk'      => $request->input('nome_categoria'),
            'id_produto_fk'        => $produtoId->id_produto
        ]);
    }

    public function buscar(Request $request)
    {
        if (Gate::allows('permissao')) {
            $produtos = Produto::where('nome_produto', 'like', '%' . $request->input('nome_produto') . '%')->paginate(15);
        } else {
            $produtos = Produto::where('nome_produto', 'like', '%' . $request->input('nome_produto') . '%')->where('status', 1)->paginate(15);
        }

        return $produtos;
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

    public function buscarProduto()
    {
        return $this->model()::get();
    }
}
