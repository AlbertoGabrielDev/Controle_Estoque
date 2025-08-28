<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartUpsertRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function __construct(private StockService $stock)
    {
    }

    public function upsert(CartUpsertRequest $request)
    {
        $client = $request->input('client');
        $items = $request->input('items');

        $priced = $this->stock->checkAndPrice($items);

        return DB::transaction(function () use ($client, $priced) {
            $cart = Cart::firstOrCreate(
                ['client' => $client, 'status' => 'open'],
                ['total_valor' => 0]
            );

            // zera itens e re-insere para ficar idempotente
            $cart->items()->delete();

            foreach ($priced['linhas'] as $linha) {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'cod_produto' => $linha['cod_produto'],
                    'nome_produto' => $linha['nome_produto'],
                    'preco_unit' => $linha['preco_unit'],
                    'quantidade' => $linha['quantidade'],
                    'subtotal_valor' => $linha['subtotal_valor'],
                ]);
            }
            $cart->total_valor = $priced['total_valor'];
            $cart->save();

            return response()->json($cart->load('items'));
        });
    }

    public function getByclient(string $client)
    {
        $cart = Cart::with('items')->where('client', $client)->where('status', 'open')->first();
        if (!$cart)
            return response()->json(['message' => 'Carrinho não encontrado'], 404);
        return response()->json($cart);
    }

    public function remove(Request $request)
    {
        $data = $request->validate([
            'client' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.sku' => 'required|string',          // aceita sku vindo do n8n
            'items.*.qty' => 'nullable|integer|min:0',   // null/0 => remove linha inteira
        ]);

        $client = (string) $data['client'];
        $items = $data['items'];
        $removed = [];   // <<— log do que saiu

        DB::transaction(function () use ($client, $items, &$removed) {
            $cart = DB::table('carts')
                ->where('client', $client)
                ->where('status', 'open')
                ->first();

            if (!$cart)
                return;

            foreach ($items as $it) {
                $cod = strtoupper((string) $it['sku']);
                $req = $it['qty'] ?? null;

                $row = DB::table('cart_items')
                    ->where('cart_id', $cart->id)
                    ->where('cod_produto', $cod)
                    ->first();

                if (!$row) {
                    $removed[] = [
                        'cod_produto' => $cod,
                        'nome_produto' => null,
                        'preco_unit' => 0,
                        'removed_qty' => 0,
                        'old_qty' => 0,
                        'new_qty' => 0,
                        'removed_subtotal_valor' => 0,
                        'action' => 'not_found',
                    ];
                    continue;
                }

                $old = (int) $row->quantidade;
                $pu = (float) $row->preco_unit;

                if ($req === null || (int) $req <= 0 || (int) $req >= $old) {
                    // remove linha inteira
                    DB::table('cart_items')->where('id', $row->id)->delete();
                    $removedQty = $old;
                    $newQty = 0;
                    $action = 'deleted';
                } else {
                    // decrementa
                    $removedQty = (int) $req;
                    $newQty = $old - $removedQty;
                    DB::table('cart_items')->where('id', $row->id)->update([
                        'quantidade' => $newQty,
                        'subtotal_valor' => round($pu * $newQty, 2),
                        'updated_at' => now(),
                    ]);
                    $action = 'decremented';
                }

                $removed[] = [
                    'cod_produto' => $cod,
                    'nome_produto' => $row->nome_produto ?? null,
                    'preco_unit' => $pu,
                    'removed_qty' => $removedQty,
                    'old_qty' => $old,
                    'new_qty' => $newQty,
                    'removed_subtotal_valor' => round($pu * $removedQty, 2),
                    'action' => $action,
                ];
            }

            // recalcula total
            $newTotal = (float) DB::table('cart_items')
                ->where('cart_id', $cart->id)
                ->sum('subtotal_valor');

            DB::table('carts')
                ->where('id', $cart->id)
                ->update(['total_valor' => $newTotal, 'updated_at' => now()]);
        });

        // resposta com carrinho + removidos
        $cart = DB::table('carts')->where('client', $client)->where('status', 'open')->first();
        $itemsResp = $cart ? DB::table('cart_items')->where('cart_id', $cart->id)->get() : collect();

        return response()->json([
            'client' => $client,
            'status' => $cart->status ?? 'open',
            'total_valor' => (float) ($cart->total_valor ?? 0),
            'items' => $itemsResp,
            'removed' => $removed,   // <<— o n8n vai usar isto
        ]);
    }
}
