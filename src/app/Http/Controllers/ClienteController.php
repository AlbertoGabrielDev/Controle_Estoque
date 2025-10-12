<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteStoreRequest;
use App\Http\Requests\ClienteUpdateRequest;
use App\Models\Cliente;
use App\Repositories\ClienteRepository;
use App\Models\CustomerSegment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\DataTableService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
class ClienteController extends Controller
{

    public function __construct(
        private DataTableService $dt,
        private ClienteRepository $clientes,
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('Clients/Index', [
            'filters' => [
                // 'q' => (string) $request->query('q', ''),
                'uf' => (string) $request->query('uf', ''),
                'segment_id' => (string) $request->query('segment_id', ''),
                'status' => (string) $request->query('status', ''),
            ],
            'segmentos' => CustomerSegment::select('id', 'nome')->orderBy('nome')->get(),
            'ufs' => ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'],
        ]);
    }

    public function data(Request $request)
    {
        // request()->attributes->set('currentMenuSlug', 'clientes');
        [$query, $columnsMap] = Cliente::makeDatatableQuery($request);
        
        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    $editBtn = Blade::render(
                        '<x-edit-button :route="$route" :model-id="$id" />',
                        ['route' => 'clientes.edit', 'id' => $row->id]
                    );
                    $statusBtn = Blade::render(
                        '<x-button-status :model-id="$id" :status="$st" model-name="cliente" />',
                        ['id' => $row->id, 'st' => (bool) $row->st]
                    );
                    $html = trim($editBtn . $statusBtn);
                    if ($html === '') {
                        $html = '<span class="inline-block w-8 h-8 opacity-0" aria-hidden="true">&nbsp;</span>';
                    }
                    return '<div class="flex gap-2 justify-start items-center">' . $html . '</div>';
                });
            }
        );
    }


    public function create()
    {
        return Inertia::render('Clients/Create', [
            'segmentos' => $this->clientes->getSegments(),
            'ufs' => $this->clientes->ufs(),
        ]);
    }

    public function store(ClienteStoreRequest $request)
    {
        $cliente = $this->clientes->createCliente($request->validated(), $request->user()->id);
        return redirect()->route('clientes.show', $cliente)->with('success', 'Cliente criado com sucesso.');
    }

    public function show(Cliente $cliente, Request $request)
    {
        $cliente = $this->clientes->findWithRelations($cliente->id_cliente);

        $pedidosCount = method_exists($cliente, 'pedidos') ? $cliente->pedidos()->count() : 0;
        $carrinhosAbertos = method_exists($cliente, 'carrinhos') ? $cliente->carrinhos()->where('status', 'open')->count() : 0;

        return Inertia::render('Clients/Show', [
            'cliente' => $cliente,
            'metricas' => [
                'pedidos_total' => $pedidosCount,
                'carrinhos_abertos' => $carrinhosAbertos,
            ],
            'tab' => (string) $request->query('tab', 'resumo'),
        ]);
    }

    public function edit(Cliente $cliente)
    {
        return Inertia::render('Clients/Edit', [
            'cliente' => $this->clientes->findWithRelations($cliente->id_cliente),
            'segmentos' => $this->clientes->getSegments(),
            'ufs' => $this->clientes->ufs(),
        ]);
    }

    public function update(ClienteUpdateRequest $request, Cliente $cliente)
    {
        $updated = $this->clientes->updateCliente($cliente->id_cliente, $request->validated());
        return redirect()->route('clientes.show', $updated)->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Cliente $cliente)
    {
        $this->clientes->deleteCliente($cliente->id_cliente);
        return redirect()->route('clientes.index')->with('success', 'Cliente removido.');
    }

    public function autocomplete(Request $request)
    {
        $term = trim((string) $request->query('q', ''));
        return response()->json($this->clientes->autocomplete($term));
    }
}
