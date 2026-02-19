<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

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

    public function produto($categoriaId)
    {
        $categoria = Categoria::findOrFail($categoriaId)->nome_categoria;
        $produtos = Gate::allows('view_post')
            ? Categoria::findOrFail($categoriaId)->produtos()->paginate(10)
            : Categoria::findOrFail($categoriaId)->produtos()->where('status', 1)->paginate(10);

        return view('categorias.produto', compact('categoria', 'produtos'));
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
}
