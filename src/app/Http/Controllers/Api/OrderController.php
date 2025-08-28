<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderCreateRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(private StockService $stock) {}

    public function store(OrderCreateRequest $request)
    {
        $client = $request->input('client');
        $cartId = $request->input('cart_id');

        $cart = $cartId
            ? Cart::with('items')->where('id', $cartId)->where('client', $client)->where('status','open')->first()
            : Cart::with('items')->where('client', $client)->where('status','open')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Carrinho vazio ou não encontrado'], 400);
        }

        return DB::transaction(function () use ($cart, $client) {
            // Confirma estoque no momento da baixa
            foreach ($cart->items as $item) {
                $this->stock->decrementStock($item->cod_produto, (int)$item->quantidade);
            }

            $order = Order::create([
                'client' => $client,
                'cart_id'=> $cart->id,
                'status' => 'created',
                'total'  => $cart->total,
            ]);

            foreach ($cart->items as $ci) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'cod_produto'  => $ci->cod_produto,
                    'nome_produto' => $ci->nome_produto,
                    'preco_unit'   => $ci->preco_unit,
                    'quantidade'   => $ci->quantidade,
                    'subtotal_valor'     => $ci->subtotal_valor,
                ]);
            }

            $cart->status = 'ordered';
            $cart->save();

            return response()->json($order->load('items'));
        });
    }
}
