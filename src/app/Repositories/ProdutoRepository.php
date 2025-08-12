<?php

namespace App\Repositories;

use App\Http\Requests\ValidacaoProduto;
use App\Models\CategoriaProduto;
use App\Models\Produto;
use App\Http\Requests\ValidacaoProdutoEditar;
use App\Models\Categoria;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

/**
 * Interface ProdutoRepository.
 *
 * @package namespace App\Repositories;
 */
class ProdutoRepository
{
    private $model;

    public function __construct(Produto $model)
    {
        $this->model = $model;
    }
    public function model()
    {
        return $this->model;
    }

    public function getById($id)
    {
        return Produto::findOrFail($id);
    }

    public function getAll()
    {
        $produtos = Produto::paginate(15);
        return $produtos;
    }


    public function getData()
    {
        $query = Produto::query()
            ->select(['id_produto', 'cod_produto', 'nome_produto', 'descricao', 'unidade_medida', 'inf_nutriente', 'status']);

        // if (!Gate::allows(ability: 'permissao')) {
        //     $query->where('status', operator: 1);
        // }

        return DataTables::eloquent($query)
            ->editColumn('inf_nutrientes', function ($p) {
                $nutri = e($p->inf_nutrientes);
                return '
                  <div x-data="{open:false}">
                    <div @click="open = !open" class="flex items-center cursor-pointer text-cyan-600 hover:text-cyan-700">
                      <div class="w-8 transform transition-transform" :class="{ \'rotate-90\': open }">
                        <i class="fas fa-angle-down"></i>
                      </div>
                      <span class="ml-2">Informações Nutricionais</span>
                    </div>
                    <div class="pl-6 mt-2 text-gray-500" x-show="open" x-collapse>' . $nutri . '</div>
                  </div>';
            })
            ->addColumn('acoes', function ($p) {
                return view('produtos.partials.acoes', ['produto' => $p])->render();
            })
            ->rawColumns(['inf_nutrientes', 'acoes'])
            ->toJson();
    }


    public function cadastro()
    {
        $categorias = Categoria::all();
        return $categorias;
    }

    public function inserirCadastro(ValidacaoProduto $request)
    {
        $produto = Produto::create([
            'nome_produto' => $request->nome_produto,
            'cod_produto' => $request->cod_produto,
            'descricao' => $request->descricao,
            'unidade_medida' => $request->unidade_medida,
            'inf_nutrientes' => json_encode($request->inf_nutrientes),
            'qrcode' => Str::uuid(),
            'id_users_fk' => Auth::id()
        ]);
        $produtoId = Produto::latest('id_produto')->first();
        CategoriaProduto::create([
            'id_categoria_fk' => $request->input('nome_categoria'),
            'id_produto_fk' => $produtoId->id_produto
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

    public function update(ValidacaoProdutoEditar $request, $produtoId)
    {
        $produtos = Produto::where('id_produto', $produtoId)
            ->update([
                'cod_produto' => $request->cod_produto,
                'nome_produto' => $request->nome_produto,
                'qrcode' => $request->qrcode,
                'descricao' => $request->descricao,
                'inf_nutrientes' => json_encode($request->inf_nutrientes)
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
