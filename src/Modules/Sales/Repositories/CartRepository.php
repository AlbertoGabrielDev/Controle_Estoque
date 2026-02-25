<?php

namespace Modules\Sales\Repositories;

use Modules\Sales\Models\Cart;
use Modules\Sales\Models\CartItem;

interface CartRepository
{
    public function firstOrCreateOpen(string $client): Cart;

    public function loadItems(Cart $cart): Cart;

    public function findOpenWithItemsByClient(string $client): ?Cart;

    public function findMatchingItem(Cart $cart, string $codProduto, ?int $estoqueId): ?CartItem;

    public function findCartItemOrFail(Cart $cart, int $cartItemId): CartItem;

    public function createItem(Cart $cart, array $attributes): CartItem;

    public function saveItem(CartItem $item): void;

    public function deleteItem(CartItem $item): void;

    public function deleteItemById(Cart $cart, int $cartItemId): void;

    public function recalculateTotal(Cart $cart): void;

    public function markOrdered(Cart $cart): void;
}
