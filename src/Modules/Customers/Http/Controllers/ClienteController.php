<?php

namespace Modules\Customers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Customers\Http\Requests\ClienteStoreRequest;
use Modules\Customers\Http\Requests\ClienteUpdateRequest;
use Modules\Customers\Models\Cliente;
use Modules\Customers\Services\ClienteService;

class ClienteController extends Controller
{
    public function __construct(
        private DataTableService $dt,
        private ClienteService $service,
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('Clients/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'uf' => (string) $request->query('uf', ''),
                'segment_id' => (string) $request->query('segment_id', ''),
                'ativo' => (string) $request->query('ativo', ''),
            ],
            'segmentos' => $this->service->indexSegments(),
            'ufs' => $this->service->ufs(),
        ]);
    }

    public function data(Request $request)
    {
        [$query, $columnsMap] = Cliente::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('clientes.edit', $row->id),
                        DataTableActions::status('cliente.status', 'cliente', $row->id, (bool) $row->st),
                        DataTableActions::delete('clientes.destroy', $row->id),
                    ]);
                });
            }
        );
    }

    public function create()
    {
        return Inertia::render('Clients/Create', [
            ...$this->service->formOptions(),
        ]);
    }

    public function store(ClienteStoreRequest $request)
    {
        $cliente = $this->service->create($request->validated(), (int) $request->user()->id);

        return redirect()->route('clientes.show', $cliente)->with('success', 'Cliente criado com sucesso.');
    }

    public function show(Cliente $cliente, Request $request)
    {
        $payload = $this->service->showPayload($cliente->id_cliente);

        return Inertia::render('Clients/Show', [
            'cliente' => $payload['cliente'],
            'metricas' => $payload['metricas'],
            'tab' => (string) $request->query('tab', 'resumo'),
        ]);
    }

    public function edit(Cliente $cliente)
    {
        return Inertia::render('Clients/Edit', [
            'cliente' => $this->service->findWithRelations($cliente->id_cliente),
            ...$this->service->formOptions(),
        ]);
    }

    public function update(ClienteUpdateRequest $request, Cliente $cliente)
    {
        $updated = $this->service->update($cliente->id_cliente, $request->validated());

        return redirect()->route('clientes.show', $updated)->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Cliente $cliente)
    {
        $this->service->delete($cliente->id_cliente);

        return redirect()->route('clientes.index')->with('success', 'Cliente removido.');
    }

    public function autocomplete(Request $request)
    {
        $term = trim((string) $request->query('q', ''));

        return response()->json($this->service->autocomplete($term));
    }
}
