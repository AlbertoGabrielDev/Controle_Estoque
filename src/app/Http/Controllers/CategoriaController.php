<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Produto;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $query->where('status', 1);
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
                'status' => (string) $request->query('status', ''),
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
                    ]);
                });
            }
        );
    }

    public function cadastro()
    {
        return Inertia::render('Categories/Create');
    }

    public function inserirCategoria(Request $request)
    {
        $request->merge([
            'nome_categoria' => (string) $request->input('nome_categoria', $request->input('categoria', '')),
        ]);

        $validated = $request->validate([
            'nome_categoria' => 'required|max:255',
            'imagem' => 'required|image|max:4096',
        ]);

        $imageName = null;
        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            $requestImage = $request->file('imagem');
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName() . strtotime('now')) . '.' . $extension;
            $requestImage->move(public_path('img/categorias'), $imageName);
        }

        Categoria::create([
            'nome_categoria' => $validated['nome_categoria'],
            'id_users_fk' => Auth::id(),
            'imagem' => $imageName,
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
                        'produtos.unidade_medida',
                        'produtos.inf_nutriente',
                        'produtos.status',
                    ])
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
        ]);
    }

    public function salvarEditar(Request $request, $categoriaId)
    {
        $validated = $request->validate([
            'nome_categoria' => 'required|max:255',
        ]);

        Categoria::query()
            ->where('id_categoria', $categoriaId)
            ->update([
                'nome_categoria' => $validated['nome_categoria'],
            ]);

        return redirect()->route('categoria.index')->with('success', 'Editado com sucesso');
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
