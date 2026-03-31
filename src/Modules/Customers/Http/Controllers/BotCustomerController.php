<?php

namespace Modules\Customers\Http\Controllers;

use App\Http\Controllers\Bot\BaseBotController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Customers\Models\Cliente;

class BotCustomerController extends BaseBotController
{
    /**
     * Busca clientes por múltiplos filtros.
     *
     * GET /api/bot/customers?phone=5511999999999&name=joao
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'phone'    => 'nullable|string|min:8|max:20',
            'document' => 'nullable|string',
            'name'     => 'nullable|string',
            'email'    => 'nullable|string',
            'limit'    => 'nullable|integer|min:1|max:50',
        ]);

        $query = Cliente::query()->where('ativo', true);

        if ($request->filled('phone')) {
            $phone = preg_replace('/\D/', '', $request->input('phone'));
            $query->where(function ($q) use ($phone) {
                $q->where('whatsapp', 'like', "%{$phone}%")
                  ->orWhere('telefone', 'like', "%{$phone}%");
            });
        }

        if ($request->filled('document')) {
            $doc = preg_replace('/\D/', '', $request->input('document'));
            $query->where('documento', 'like', "%{$doc}%");
        }

        if ($request->filled('name')) {
            $name = $request->input('name');
            $query->where(function ($q) use ($name) {
                $q->where('nome', 'like', "%{$name}%")
                  ->orWhere('nome_fantasia', 'like', "%{$name}%")
                  ->orWhere('razao_social', 'like', "%{$name}%");
            });
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', "%{$request->input('email')}%");
        }

        $limit = $request->input('limit', 20);

        $customers = $query->with(['segmento:id,nome'])->limit($limit)->get();

        return $this->responseSuccess([
            'customers' => $customers->map(fn ($customer) => [
                'id'          => $customer->id_cliente,
                'name'        => $customer->nome ?: $customer->nome_fantasia ?: $customer->razao_social,
                'document'    => $customer->documento,
                'whatsapp'    => $customer->whatsapp,
                'email'       => $customer->email,
                'city'        => $customer->cidade,
                'state'       => $customer->uf,
                'segment'     => $customer->segmento?->nome,
                'credit_limit' => $this->formatCurrency($customer->limite_credito),
                'blocked'     => (bool) $customer->bloqueado,
            ]),
            'count' => $customers->count(),
        ]);
    }

    /**
     * Detalhes simples de um cliente.
     *
     * GET /api/bot/customers/{id}
     */
    public function show(int $id): JsonResponse
    {
        $customer = Cliente::query()
            ->where('ativo', true)
            ->with(['segmento:id,nome'])
            ->find($id);

        if (! $customer) {
            return $this->responseError('Cliente não encontrado', 404);
        }

        return $this->responseSuccess([
            'customer' => [
                'id'          => $customer->id_cliente,
                'name'        => $customer->nome ?: $customer->nome_fantasia ?: $customer->razao_social,
                'document'    => $customer->documento,
                'whatsapp'    => $customer->whatsapp,
                'email'       => $customer->email,
                'city'        => $customer->cidade,
                'state'       => $customer->uf,
                'segment'     => $customer->segmento?->nome,
                'credit_limit' => $this->formatCurrency($customer->limite_credito),
                'blocked'     => (bool) $customer->bloqueado,
            ],
        ]);
    }

    /**
     * Resumo financeiro/compras do cliente.
     *
     * GET /api/bot/customers/{id}/summary
     */
    public function summary(int $id): JsonResponse
    {
        $customer = Cliente::query()
            ->where('ativo', true)
            ->with(['segmento:id,nome', 'vendas' => function ($q) {
                $q->orderByDesc('created_at')->limit(5);
            }])
            ->find($id);

        if (! $customer) {
            return $this->responseError('Cliente não encontrado', 404);
        }

        $recentSales = $customer->vendas->map(fn ($v) => [
            'product'    => $v->nome_produto,
            'quantity'   => (float) $v->quantidade,
            'price'      => $this->formatCurrency($v->preco_venda),
            'date'       => $v->created_at?->format('Y-m-d'),
        ]);

        return $this->responseSuccess([
            'customer' => [
                'id'           => $customer->id_cliente,
                'name'         => $customer->nome ?: $customer->nome_fantasia ?: $customer->razao_social,
                'segment'      => $customer->segmento?->nome,
                'credit_limit' => $this->formatCurrency($customer->limite_credito),
                'blocked'      => (bool) $customer->bloqueado,
            ],
            'recent_sales'       => $recentSales,
            'total_recent_sales' => $this->formatCurrency($recentSales->sum('price')),
        ]);
    }
}
