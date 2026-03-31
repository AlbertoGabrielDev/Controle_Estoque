<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Bot\BaseBotController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Customers\Models\Cliente;
use Modules\Sales\Models\Order;
use Modules\Sales\Models\Venda;

class BotOrderController extends BaseBotController
{
    /**
     * Busca pedidos/vendas recentes de um cliente.
     *
     * GET /api/bot/orders?customer_cpf=12345678900
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'customer_cpf' => 'nullable|string|min:8|max:20',
            'customer_id'  => 'nullable|integer',
            'limit'        => 'nullable|integer|min:1|max:50',
        ]);

        $query = Venda::query();
        $limit = $request->input('limit', 20);

        $customer = null;

        if ($request->filled('customer_id')) {
            $customer = Cliente::find($request->input('customer_id'));
        } elseif ($request->filled('customer_cpf')) {
            $doc = preg_replace('/\D/', '', $request->input('customer_cpf'));
            $customer = Cliente::query()
                ->where('ativo', true)
                ->where('documento', $doc)
                ->first();
        }

        if ($customer) {
            $query->where('id_usuario_fk', $customer->id_users_fk);
        } else {
            // Se tentou buscar por cliente não existente
            if ($request->filled('customer_id') || $request->filled('customer_cpf')) {
                return $this->responseSuccess(['orders' => [], 'count' => 0, 'found' => false]);
            }
        }

        $vendas = $query->orderByDesc('created_at')->limit($limit)->get();

        return $this->responseSuccess([
            'found'         => $customer ? true : false,
            'customer_name' => $customer ? ($customer->nome ?: $customer->nome_fantasia) : null,
            'orders'        => $vendas->map(fn (Venda $v) => [
                'id'           => $v->id_venda,
                'product'      => $v->nome_produto,
                'product_code' => $v->cod_produto,
                'quantity'     => (float) $v->quantidade,
                'unit_price'   => $this->formatCurrency($v->preco_venda),
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
            return $this->responseError('Pedido não encontrado', 404);
        }

        return $this->responseSuccess([
            'order' => [
                'id'     => $order->id,
                'client' => $order->client,
                'status' => $order->status,
                'total'  => $this->formatCurrency($order->total_valor),
                'items'  => collect($order->items)->map(fn ($item) => [
                    'product_id' => $item->product_id,
                    'quantity'   => (float) $item->quantity,
                    'price'      => $this->formatCurrency($item->price),
                ]),
            ],
        ]);
    }
}
