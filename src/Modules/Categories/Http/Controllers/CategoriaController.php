<?php

namespace Modules\Categories\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Modules\Categories\Http\Requests\CategoriaStoreRequest;
use Modules\Categories\Http\Requests\CategoriaUpdateRequest;
use Modules\Categories\Models\Categoria;
use Modules\Categories\Services\CategoriaService;
use Modules\Products\Models\Produto;
use RuntimeException;

class CategoriaController extends Controller
{
    public function __construct(
        private DataTableService $dt,
        private CategoriaService $service
    ) {
    }

    public function inicio()
    {
        return Inertia::render('Categories/Home', [
            'categorias' => $this->service->listForHome(Gate::allows('permissao')),
        ]);
    }

    public function index(Request $request)
    {
        return Inertia::render('Categories/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'status' => (string) $request->query('status', $request->query('ativo', '')),
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
        if (!Schema::hasTable('categorias')) {
            return Inertia::render('Categories/Create', [
                'categoriasPai' => [],
            ]);
        }

        return Inertia::render('Categories/Create', [
            'categoriasPai' => $this->service->listParentOptions(),
        ]);
    }

    public function inserirCategoria(CategoriaStoreRequest $request)
    {
        $this->service->create($request->validated(), $request->file('imagem'), auth()->id());

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
            'categoria' => $this->service->findOrFail((int) $categoriaId),
            'categoriasPai' => $this->service->listParentOptions((int) $categoriaId),
        ]);
    }

    public function salvarEditar(CategoriaUpdateRequest $request, $categoriaId)
    {
        $this->service->update((int) $categoriaId, $request->validated(), $request->file('imagem'));

        return redirect()->route('categoria.index')->with('success', 'Editado com sucesso');
    }

    public function destroy($categoriaId)
    {
        try {
            $this->service->delete((int) $categoriaId);
        } catch (RuntimeException $e) {
            return redirect()
                ->route('categoria.index')
                ->with('error', $e->getMessage());
        }

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
