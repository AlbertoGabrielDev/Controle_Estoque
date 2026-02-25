<?php

namespace Modules\Sales\Repositories;

use Modules\Sales\Models\Cart;
use Modules\Sales\Models\CartItem;

class CartRepositoryEloquent implements CartRepository
{
    public function firstOrCreateOpen(string $client): Cart
    {
        return Cart::firstOrCreate(
            ['client' => $client, 'status' => 'open'],
            ['total_valor' => 0]
        );
    }

    public function loadItems(Cart $cart): Cart
    {
        return $cart->load('items');
    }

    public function findOpenWithItemsByClient(string $client): ?Cart
    {
        return Cart::with('items')
            ->where('client', $client)
            ->where('status', 'open')
            ->first();
    }

    public function findMatchingItem(Cart $cart, string $codProduto, ?int $estoqueId): ?CartItem
    {
        $query = $cart->items()->where('cod_produto', $codProduto);

        if ($estoqueId) {
            $query->where('id_estoque_fk', $estoqueId);
        } else {
            $query->whereNull('id_estoque_fk');
        }

        return $query->first();
    }

    public function findCartItemOrFail(Cart $cart, int $cartItemId): CartItem
    {
        return $cart->items()->where('id', $cartItemId)->firstOrFail();
    }

    public function createItem(Cart $cart, array $attributes): CartItem
    {
        /** @var CartItem $item */
        $item = $cart->items()->create($attributes);

        return $item;
    }

    public function saveItem(CartItem $item): void
    {
        $item->save();
    }

    public function deleteItem(CartItem $item): void
    {
        $item->delete();
    }

    public function deleteItemById(Cart $cart, int $cartItemId): void
    {
        $cart->items()->where('id', $cartItemId)->delete();
    }

    public function recalculateTotal(Cart $cart): void
    {
        $cart->total_valor = (float) $cart->items()->sum('subtotal_valor');
        $cart->save();
    }

    public function markOrdered(Cart $cart): void
    {
        $cart->status = 'ordered';
        $cart->save();
    }
}
