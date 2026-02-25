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
        $nutrition = $this->normalizeNutrition($request->input('inf_nutriente'));
        $produto = Produto::create([
            'nome_produto' => $request->nome_produto,
            'cod_produto' => $request->cod_produto,
            'descricao' => $request->descricao,
            'unidade_medida' => $unidadeCodigo,
            'unidade_medida_id' => $request->unidade_medida_id,
            'item_id' => $request->item_id,
            'inf_nutriente' => $nutrition,
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
        $nutrition = $this->normalizeNutrition($request->input('inf_nutriente'));
  
        $produto->update([
            'cod_produto' => $request->cod_produto,
            'nome_produto' => $request->nome_produto,
            'descricao' => $request->descricao,
            'unidade_medida' => $unidadeCodigo,
            'unidade_medida_id' => $request->unidade_medida_id,
            'item_id' => $request->item_id,
            'inf_nutriente' => $nutrition, // <-- coluna JSON no banco
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
    private function normalizeNutrition(?string $value): ?array
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $decoded = json_decode($value, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [$this->makeNutritionItem('Texto', trim($value), null)];
        }

        $normalized = $this->normalizeNutritionValue($decoded);
        return $normalized !== [] ? $normalized : null;
    }

    private function normalizeNutritionValue(mixed $decoded): array
    {
        if (is_array($decoded)) {
            if (array_is_list($decoded)) {
                $items = [];
                foreach ($decoded as $item) {
                    $normalized = $this->normalizeNutritionItem($item);
                    if ($normalized !== null) {
                        $items[] = $normalized;
                    }
                }
                return $items;
            }

            $items = [];
            foreach ($decoded as $key => $value) {
                $items[] = $this->makeNutritionItem(
                    $this->labelFromKey((string) $key),
                    $value,
                    $this->unitFromKey((string) $key),
                );
            }
            return $items;
        }

        return [$this->makeNutritionItem('Texto', $decoded, null)];
    }

    private function normalizeNutritionItem(mixed $item): ?array
    {
        if ($item === null || $item === '') {
            return null;
        }

        if (!is_array($item)) {
            return $this->makeNutritionItem('Item', $item, null);
        }

        $label = $item['label'] ?? $item['nome'] ?? $item['chave'] ?? $item['key'] ?? $item['nutriente'] ?? null;
        $value = $item['valor'] ?? $item['value'] ?? $item['quantidade'] ?? $item['qtd'] ?? null;
        $unit = $item['unidade'] ?? $item['unit'] ?? null;

        if ($label === null && $value === null) {
            return null;
        }

        $labelText = is_string($label) && $label !== '' ? $label : 'Item';
        return $this->makeNutritionItem($labelText, $value, $unit);
    }

    private function makeNutritionItem(string $label, mixed $valor, ?string $unidade): array
    {
        return [
            'label' => $label,
            'valor' => $valor,
            'unidade' => $unidade,
        ];
    }

    private function labelFromKey(string $key): string
    {
        $clean = trim(str_replace('_', ' ', $key));
        return $clean === '' ? 'Item' : ucwords($clean);
    }

    private function unitFromKey(string $key): ?string
    {
        $map = [
            'calorias' => 'kcal',
            'proteina' => 'g',
            'carboidrato' => 'g',
            'gordura' => 'g',
            'fibra' => 'g',
            'sodio' => 'mg',
            'acucar' => 'g',
        ];

        $k = strtolower(trim($key));
        return $map[$k] ?? null;
    }
}
