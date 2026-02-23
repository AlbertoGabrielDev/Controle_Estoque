<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Produto;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use App\Http\Requests\CategoriaStoreRequest;
use App\Http\Requests\CategoriaUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class CategoriaController extends Controller
{
    public function __construct(
        private DataTableService $dt
    ) {
    }

    public function inicio()
    {
        $query = Categoria::query()->withCount('produtos');

        if (!Gate::allows('permissao')) {
            $query->where('ativo', 1);
        }

        $categorias = $query
            ->orderBy('nome_categoria')
            ->get(['id_categoria', 'nome_categoria', 'imagem']);

        return Inertia::render('Categories/Home', [
            'categorias' => $categorias,
        ]);
    }

    public function index(Request $request)
    {
        return Inertia::render('Categories/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'ativo' => (string) $request->query('ativo', ''),
            ],
        ]);
    }

    public function data(Request $request)
    {
        [$query, $columnsMap] = Categoria::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('categorias.editar', $row->id),
                        DataTableActions::status('categoria.status', 'categoria', $row->id, (bool) $row->st),
                        DataTableActions::delete('categorias.destroy', $row->id),
                    ]);
                });
            }
        );
    }

    public function cadastro()
    {
        return Inertia::render('Categories/Create', [
            'categoriasPai' => Categoria::query()
                ->select('id_categoria', 'nome_categoria')
                ->orderBy('nome_categoria')
                ->get(),
        ]);
    }

    public function inserirCategoria(CategoriaStoreRequest $request)
    {
        $validated = $request->validated();

        $imageName = null;
        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            $requestImage = $request->file('imagem');
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName() . strtotime('now')) . '.' . $extension;
            $requestImage->move(public_path('img/categorias'), $imageName);
        }

        Categoria::create([
            'codigo' => $validated['codigo'],
            'nome_categoria' => $validated['nome_categoria'],
            'tipo' => $validated['tipo'],
            'categoria_pai_id' => $validated['categoria_pai_id'] ?? null,
            'id_users_fk' => Auth::id(),
            'imagem' => $imageName,
            'ativo' => (bool) $validated['ativo'],
        ]);

        return redirect()->route('categoria.index')->with('success', 'Inserido com sucesso');
    }

    public function produto(int|string $categoriaId): InertiaResponse
    {
        return Inertia::render('Categories/Products', [
            'categoriaId' => (int) $categoriaId,
            'categoria' => fn () => (string) Categoria::query()
                ->whereKey($categoriaId)
                ->value('nome_categoria'),
            'produtos' => function () use ($categoriaId) {
                $categoria = Categoria::query()->findOrFail($categoriaId);
                $query = $categoria->produtos()
                    ->select([
                        'produtos.id_produto',
                        'produtos.cod_produto',
                        'produtos.nome_produto',
                        'produtos.descricao',
                        DB::raw('COALESCE(unidades_medida.codigo, produtos.unidade_medida) as unidade_medida'),
                        'produtos.inf_nutriente',
                        'produtos.status',
                    ])
                    ->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'produtos.unidade_medida_id')
                    ->orderBy('produtos.nome_produto');

                if (!Gate::allows('permissao')) {
                    $query->where('produtos.status', 1);
                }

                return $query->paginate(10)->through(function (Produto $produto): array {
                    return [
                        'id_produto' => (int) $produto->id_produto,
                        'cod_produto' => (string) $produto->cod_produto,
                        'nome_produto' => (string) $produto->nome_produto,
                        'descricao' => (string) $produto->descricao,
                        'unidade_medida' => (string) $produto->unidade_medida,
                        'inf_nutriente' => $this->normalizeNutrition($produto->inf_nutriente),
                        'status' => (int) $produto->status,
                    ];
                });
            },
        ]);
    }

    public function editar($categoriaId)
    {
        return Inertia::render('Categories/Edit', [
            'categoria' => Categoria::query()->findOrFail($categoriaId),
            'categoriasPai' => Categoria::query()
                ->where('id_categoria', '<>', $categoriaId)
                ->select('id_categoria', 'nome_categoria')
                ->orderBy('nome_categoria')
                ->get(),
        ]);
    }

    public function salvarEditar(CategoriaUpdateRequest $request, $categoriaId)
    {
        $validated = $request->validated();

        $imageName = null;
        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            $requestImage = $request->file('imagem');
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName() . strtotime('now')) . '.' . $extension;
            $requestImage->move(public_path('img/categorias'), $imageName);
        }

        $data = [
            'codigo' => $validated['codigo'],
            'nome_categoria' => $validated['nome_categoria'],
            'tipo' => $validated['tipo'],
            'categoria_pai_id' => $validated['categoria_pai_id'] ?? null,
            'ativo' => (bool) $validated['ativo'],
        ];

        if ($imageName) {
            $data['imagem'] = $imageName;
        }

        Categoria::query()->where('id_categoria', $categoriaId)->update($data);

        return redirect()->route('categoria.index')->with('success', 'Editado com sucesso');
    }

    public function destroy($categoriaId)
    {
        $categoria = Categoria::query()
            ->withCount(['produtos', 'filhas'])
            ->findOrFail($categoriaId);

        if ($categoria->produtos_count > 0 || $categoria->filhas_count > 0) {
            return redirect()
                ->route('categoria.index')
                ->with('error', 'Não é possível remover: há produtos ou subcategorias vinculadas.');
        }

        $categoria->delete();

        return redirect()->route('categoria.index')->with('success', 'Categoria removida.');
    }

    private function normalizeNutrition(mixed $value): mixed
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }

            return trim($value);
        }

        return $value;
    }
}
