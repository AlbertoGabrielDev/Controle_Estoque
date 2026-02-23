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
use App\Support\DataTableActions;
use Illuminate\Support\Facades\DB;
use App\Models\UnidadeMedida;
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
        request()->attributes->set('currentMenuSlug', 'produtos');

        $query = Produto::query()->select([
            'id_produto        as id',
            'cod_produto       as c1',
            'nome_produto      as c2',
            'descricao         as c3',
            DB::raw('COALESCE(um.codigo, produtos.unidade_medida) as c4'),
            'inf_nutriente     as c5',
            'status            as st',
        ])->leftJoin('unidades_medida as um', 'um.id', '=', 'produtos.unidade_medida_id');

        $dt = DataTables::eloquent($query)
            ->orderColumn('c1', 'cod_produto $1')
            ->orderColumn('c2', 'nome_produto $1')
            ->orderColumn('c3', 'descricao $1')
            ->orderColumn('c4', 'COALESCE(um.codigo, produtos.unidade_medida) $1')
            ->filterColumn('c1', fn($q, $k) => $q->where('cod_produto', 'like', "%{$k}%"))
            ->filterColumn('c2', fn($q, $k) => $q->where('nome_produto', 'like', "%{$k}%"))
            ->filterColumn('c3', fn($q, $k) => $q->where('descricao', 'like', "%{$k}%"))
            ->filterColumn('c4', function ($q, $k) {
                $q->where(function ($w) use ($k) {
                    $w->where('um.codigo', 'like', "%{$k}%")
                        ->orWhere('produtos.unidade_medida', 'like', "%{$k}%");
                });
            })

            ->addColumn('acoes', function ($p) {
                return DataTableActions::wrap([
                    DataTableActions::edit('produtos.editar', $p->id),
                    DataTableActions::status('produto.status', 'produto', $p->id, (bool) $p->st),
                ], 'end');
            })
            ->rawColumns(['acoes']);

        $json = $dt->toJson();
        $payload = $json->getData(true);
        foreach (['queries', 'input', 'options', 'debug', 'request'] as $k)
            unset($payload[$k]);
        return response()->json($payload);
    }

    public function cadastro()
    {
        $categorias = Categoria::all();
        return $categorias;
    }

    public function inserirCadastro(ValidacaoProduto $request)
    {
        $unidade = UnidadeMedida::query()->find($request->unidade_medida_id);
        $unidadeCodigo = $unidade?->codigo;

        $produto = Produto::create([
            'nome_produto' => $request->nome_produto,
            'cod_produto' => $request->cod_produto,
            'descricao' => $request->descricao,
            'unidade_medida' => $unidadeCodigo,
            'unidade_medida_id' => $request->unidade_medida_id,
            'item_id' => $request->item_id,
            'inf_nutriente' => $request->inf_nutriente ? json_encode($request->inf_nutriente) : null,
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
        $produto = Produto::with('categorias')->where('id_produto', $produtoId)->firstOrFail();

        $categorias = Categoria::select('id_categoria', 'nome_categoria')->orderBy('nome_categoria')->get();
        return [
            'produtos' => collect([$produto]),
            'categorias' => $categorias,
        ];
    }
    public function update(ValidacaoProdutoEditar $request, $produtoId)
    {
        $produto = Produto::findOrFail($produtoId);
        $unidade = UnidadeMedida::query()->find($request->unidade_medida_id);
        $unidadeCodigo = $unidade?->codigo;

        // Normaliza o JSON vindo do textarea (string -> array) por segurança:
        $inf = $request->input('inf_nutriente');
        if (is_string($inf) && $inf !== '') {
            $decoded = json_decode($inf, true);
            // se JSON inválido, $decoded será null; a FormRequest já barrou antes (regra 'json')
        } else {
            $decoded = null; // permite limpar
        }
  
        $produto->update([
            'cod_produto' => $request->cod_produto,
            'nome_produto' => $request->nome_produto,
            'qrcode' => $request->qrcode,
            'descricao' => $request->descricao,
            'unidade_medida' => $unidadeCodigo,
            'unidade_medida_id' => $request->unidade_medida_id,
            'item_id' => $request->item_id,
            'inf_nutriente' => $decoded, // <-- coluna JSON no banco
        ]);

        if ($request->filled('id_categoria_fk')) {
            // atualiza a TABELA PIVOT (categoria_produtos)
            $produto->categorias()->sync([$request->id_categoria_fk]);
        }

        return $produto;
    }

    public function statusInativar($statusId)
    {
        $status = Produto::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return $status;
    }
}
