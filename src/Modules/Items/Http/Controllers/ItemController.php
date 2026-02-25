<?php

namespace Modules\Items\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UnidadeMedida;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Categories\Models\Categoria;
use Modules\Items\Http\Requests\ItemStoreRequest;
use Modules\Items\Http\Requests\ItemUpdateRequest;
use Modules\Items\Models\Item;
use Modules\Items\Services\ItemService;

class ItemController extends Controller
{
    public function __construct(
        private DataTableService $dt,
        private ItemService $service
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('Items/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'tipo' => (string) $request->query('tipo', ''),
                'ativo' => (string) $request->query('ativo', ''),
            ],
        ]);
    }

    public function data(Request $request)
    {
        [$query, $columnsMap] = Item::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('itens.edit', $row->id),
                        DataTableActions::status('itens.status', 'item', $row->id, (bool) $row->st),
                        DataTableActions::delete('itens.destroy', $row->id),
                    ]);
                });
            }
        );
    }

    public function create()
    {
        return Inertia::render('Items/Create', [
            'categorias' => Categoria::query()
                ->select('id_categoria', 'nome_categoria', 'tipo')
                ->orderBy('nome_categoria')
                ->get(),
            'unidades' => UnidadeMedida::query()
                ->select('id', 'codigo', 'descricao')
                ->orderBy('codigo')
                ->get(),
        ]);
    }

    public function store(ItemStoreRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('itens.index')->with('success', 'Item criado com sucesso.');
    }

    public function edit(Item $item)
    {
        return Inertia::render('Items/Edit', [
            'item' => $item,
            'categorias' => Categoria::query()
                ->select('id_categoria', 'nome_categoria', 'tipo')
                ->orderBy('nome_categoria')
                ->get(),
            'unidades' => UnidadeMedida::query()
                ->select('id', 'codigo', 'descricao')
                ->orderBy('codigo')
                ->get(),
        ]);
    }

    public function update(ItemUpdateRequest $request, Item $item)
    {
        $this->service->update($item, $request->validated());

        return redirect()->route('itens.index')->with('success', 'Item atualizado com sucesso.');
    }

    public function destroy(Item $item)
    {
        $this->service->delete($item);

        return redirect()->route('itens.index')->with('success', 'Item removido.');
    }
}
