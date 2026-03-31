<?php

namespace Modules\Customers\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Customers\Models\Cliente;

class BotCustomerController
{
    /**
     * Busca cliente por telefone/WhatsApp.
     *
     * GET /api/bot/customers?phone=5511999999999
     */
    public function byPhone(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|min:8|max:20',
        ]);

        $phone = preg_replace('/\D/', '', $request->input('phone'));

        $customer = Cliente::query()
            ->where('ativo', true)
            ->where(function ($q) use ($phone) {
                $q->where('whatsapp', 'like', "%{$phone}%")
                  ->orWhere('telefone', 'like', "%{$phone}%");
            })
            ->with(['segmento:id,nome'])
            ->first();

        if (! $customer) {
            return response()->json(['customer' => null, 'found' => false]);
        }

        return response()->json([
            'found'    => true,
            'customer' => [
                'id'          => $customer->id_cliente,
                'name'        => $customer->nome ?: $customer->nome_fantasia ?: $customer->razao_social,
                'document'    => $customer->documento,
                'whatsapp'    => $customer->whatsapp,
                'email'       => $customer->email,
                'city'        => $customer->cidade,
                'state'       => $customer->uf,
                'segment'     => $customer->segmento?->nome,
                'credit_limit' => (float) $customer->limite_credito,
                'blocked'     => (bool) $customer->bloqueado,
            ],
        ]);
    }

    /**
     * Resumo do cliente (compras recentes, saldo).
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
            return response()->json(['error' => 'Cliente não encontrado'], 404);
        }

        $recentSales = $customer->vendas->map(fn ($v) => [
            'product'    => $v->nome_produto,
            'quantity'   => (float) $v->quantidade,
            'price'      => (float) $v->preco_venda,
            'date'       => $v->created_at?->format('Y-m-d'),
        ]);

        return response()->json([
            'customer' => [
                'name'         => $customer->nome ?: $customer->nome_fantasia ?: $customer->razao_social,
                'segment'      => $customer->segmento?->nome,
                'credit_limit' => (float) $customer->limite_credito,
                'blocked'      => (bool) $customer->bloqueado,
            ],
            'recent_sales'     => $recentSales,
            'total_recent_sales' => $recentSales->sum('price'),
        ]);
    }
}
