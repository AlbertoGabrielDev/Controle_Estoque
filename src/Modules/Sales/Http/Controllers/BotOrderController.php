<?php

namespace Modules\Sales\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Customers\Models\Cliente;
use Modules\Sales\Models\Order;
use Modules\Sales\Models\Venda;

class BotOrderController
{
    /**
     * Pedidos recentes de um cliente por documento (CPF/CNPJ).
     *
     * GET /api/bot/orders?customer_cpf=12345678900
     */
    public function byCustomerDocument(Request $request): JsonResponse
    {
        $request->validate([
            'customer_cpf' => 'required|string|min:8|max:20',
        ]);

        $doc = preg_replace('/\D/', '', $request->input('customer_cpf'));

        $customer = Cliente::query()
            ->where('ativo', true)
            ->where('documento', $doc)
            ->first();

        if (! $customer) {
            return response()->json([
                'found'  => false,
                'orders' => [],
            ]);
        }

        // Busca vendas recentes do cliente
        $vendas = Venda::query()
            ->where('id_usuario_fk', $customer->id_users_fk)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()->json([
            'found'         => true,
            'customer_name' => $customer->nome ?: $customer->nome_fantasia,
            'orders'        => $vendas->map(fn (Venda $v) => [
                'product'      => $v->nome_produto,
                'product_code' => $v->cod_produto,
                'quantity'     => (float) $v->quantidade,
                'unit_price'   => (float) $v->preco_venda,
                'unit'         => $v->unidade_medida,
                'date'         => $v->created_at?->format('Y-m-d H:i'),
            ]),
            'count' => $vendas->count(),
        ]);
    }

    /**
     * Detalhes de um pedido (order) específico.
     *
     * GET /api/bot/orders/{id}
     */
    public function show(int $id): JsonResponse
    {
        $order = Order::with('items')->find($id);

        if (! $order) {
            return response()->json(['error' => 'Pedido não encontrado'], 404);
        }

        return response()->json([
            'order' => [
                'id'     => $order->id,
                'client' => $order->client,
                'status' => $order->status,
                'total'  => (float) $order->total_valor,
                'items'  => $order->items->map(fn ($item) => [
                    'product_id' => $item->product_id,
                    'quantity'   => (float) $item->quantity,
                    'price'      => (float) $item->price,
                ]),
            ],
        ]);
    }
}
