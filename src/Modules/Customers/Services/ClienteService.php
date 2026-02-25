<?php

namespace Modules\Customers\Services;

use App\Models\Tax;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Modules\Customers\Models\Cliente;
use Modules\Customers\Models\CustomerSegment;
use Modules\Customers\Repositories\ClienteRepository;
use Modules\PriceTables\Models\TabelaPreco;

class ClienteService
{
    public function __construct(private ClienteRepository $clientes)
    {
    }

    public function formOptions(): array
    {
        return [
            'segmentos' => $this->indexSegments(),
            'ufs' => $this->clientes->ufs(),
            'tabelasPreco' => Schema::hasTable('tabelas_preco')
                ? TabelaPreco::query()
                    ->select('id', 'codigo', 'nome')
                    ->orderBy('nome')
                    ->get()
                : collect(),
            'impostos' => Schema::hasTable('taxes')
                ? Tax::query()
                    ->where('ativo', true)
                    ->select('id', 'codigo', 'nome')
                    ->orderBy('nome')
                    ->get()
                : collect(),
        ];
    }

    public function indexSegments(): Collection
    {
        if (!Schema::hasTable('customer_segments')) {
            return collect();
        }

        return CustomerSegment::query()->select('id', 'nome')->orderBy('nome')->get();
    }

    public function create(array $data, int $userId): Cliente
    {
        return $this->clientes->createCliente($data, $userId);
    }

    public function findWithRelations(int|string $id): Cliente
    {
        return $this->clientes->findWithRelations($id);
    }

    public function showPayload(int|string $id): array
    {
        $cliente = $this->findWithRelations($id);

        $pedidosCount = method_exists($cliente, 'pedidos') ? $cliente->pedidos()->count() : 0;
        $carrinhosAbertos = method_exists($cliente, 'carrinhos')
            ? $cliente->carrinhos()->where('status', 'open')->count()
            : 0;

        return [
            'cliente' => $cliente,
            'metricas' => [
                'pedidos_total' => $pedidosCount,
                'carrinhos_abertos' => $carrinhosAbertos,
            ],
        ];
    }

    public function update(int|string $id, array $data): Cliente
    {
        return $this->clientes->updateCliente($id, $data);
    }

    public function delete(int|string $id): bool
    {
        return $this->clientes->deleteCliente($id);
    }

    public function autocomplete(string $term)
    {
        return $this->clientes->autocomplete($term);
    }

    public function ufs(): array
    {
        return $this->clientes->ufs();
    }
}
