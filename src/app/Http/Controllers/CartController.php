<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartUpsertRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function __construct(private StockService $stock) {}

    public function upsert(CartUpsertRequest $request)
    {
        $msisdn = $request->input('msisdn');
        $items  = $request->input('items');

        $priced = $this->stock->checkAndPrice($items);

        return DB::transaction(function () use ($msisdn, $priced) {
            $cart = Cart::firstOrCreate(
                ['msisdn' => $msisdn, 'status' => 'open'],
                ['total' => 0]
            );

            // zera itens e re-insere para ficar idempotente
            $cart->items()->delete();

            foreach ($priced['linhas'] as $linha) {
                CartItem::create([
                    'cart_id'      => $cart->id,
                    'cod_produto'  => $linha['cod_produto'],
                    'nome_produto' => $linha['nome_produto'],
                    'preco_unit'   => $linha['preco_unit'],
                    'quantidade'   => $linha['quantidade'],
                    'subtotal'     => $linha['subtotal'],
                ]);
            }
            $cart->total = $priced['total'];
            $cart->save();

            return response()->json($cart->load('items'));
        });
    }

    public function getByMsisdn(string $msisdn)
    {
        $cart = Cart::with('items')->where('msisdn', $msisdn)->where('status','open')->first();
        if (!$cart) return response()->json(['message' => 'Carrinho não encontrado'], 404);
        return response()->json($cart);
    }
}
