<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteStoreRequest;
use App\Models\Cliente;
use App\Repositories\ClienteRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClienteController extends Controller
{
    public function __construct(private ClienteRepository $clientes) {}

    public function index(Request $request)
    {
        $filters = [
            'q'          => $request->string('q')->toString(),
            'uf'         => $request->string('uf')->toString(),
            'segment_id' => $request->integer('segment_id'),
            'status'     => $request->has('status') ? $request->integer('status') : null,
            'per_page'   => 10,
        ];

        return Inertia::render('Clients/Index', [
            'filters'   => [
                'q' => $filters['q'],
                'uf' => $filters['uf'],
                'segment_id' => $filters['segment_id'],
                'status' => $filters['status'],
            ],
            'clientes'  => $this->clientes->paginateWithFilters($filters),
            'segmentos' => $this->clientes->getSegments(),
            'ufs'       => $this->clientes->ufs(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Clients/Create', [
            'segmentos' => $this->clientes->getSegments(),
            'ufs'       => $this->clientes->ufs(),
        ]);
    }

    public function store(ClienteStoreRequest $request)
    {
        $cliente = $this->clientes->createWithUser($request->validated(), $request->user()->id);
        return redirect()->route('clientes.show', $cliente)->with('success','Cliente criado com sucesso.');
    }

    public function show(Cliente $cliente, Request $request)
    {
        $cliente = $this->clientes->findWithRelations($cliente->id_cliente);

        $pedidosCount     = method_exists($cliente, 'pedidos')  ? $cliente->pedidos()->count() : 0;
        $carrinhosAbertos = method_exists($cliente, 'carrinhos')? $cliente->carrinhos()->where('status','open')->count() : 0;

        return Inertia::render('Clients/Show', [
            'cliente'  => $cliente,
            'metricas' => [
                'pedidos_total' => $pedidosCount,
                'carrinhos_abertos' => $carrinhosAbertos,
            ],
            'tab'      => $request->string('tab','resumo')->toString(),
        ]);
    }

    public function edit(Cliente $cliente)
    {
        return Inertia::render('Clients/Edit', [
            'cliente'   => $this->clientes->findWithRelations($cliente->id_cliente),
            'segmentos' => $this->clientes->getSegments(),
            'ufs'       => $this->clientes->ufs(),
        ]);
    }
    public function update(Request $request, Cliente $cliente)
    {
        $this->clientes->updateCliente($cliente, $request->validated());
        return redirect()->route('clientes.show', $cliente)->with('success','Cliente atualizado com sucesso.');
    }

    public function destroy(Cliente $cliente)
    {
        $this->clientes->deleteCliente($cliente);
        return redirect()->route('clientes.index')->with('success','Cliente removido.');
    }

    public function autocomplete(Request $request)
    {
        $term = $request->string('q')->toString();
        return response()->json($this->clientes->autocomplete($term));
    }
}
