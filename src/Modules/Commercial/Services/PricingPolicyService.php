<?php

namespace Modules\Commercial\Services;

use App\Models\Item;
use Modules\Commercial\Models\CommercialDiscountPolicy;
use Modules\Customers\Models\Cliente;
use Modules\PriceTables\Models\TabelaPreco;
use RuntimeException;

class PricingPolicyService
{
    /**
     * Resolve the base unit price for an item, considering the customer's price table if available.
     *
     * Resolution order:
     * 1. Price table entry for the customer's assigned table, if it exists.
     * 2. Item's default sale price (preco_venda).
     *
     * @param int      $itemId
     * @param int|null $clienteId
     * @return float
     */
    public function resolveItemPrice(int $itemId, ?int $clienteId = null): float
    {
        $item = Item::query()->findOrFail($itemId);

        if ($clienteId) {
            $cliente = Cliente::query()
                ->where('id_cliente', $clienteId)
                ->first();

            if ($cliente && $cliente->tabela_preco_id) {
                $tabelaPreco = TabelaPreco::query()
                    ->find($cliente->tabela_preco_id);

                if ($tabelaPreco) {
                    $entry = $tabelaPreco->items()
                        ->where('item_id', $itemId)
                        ->first();

                    if ($entry) {
                        return (float) $entry->preco;
                    }
                }
            }
        }

        return (float) ($item->preco_venda ?? 0);
    }

    /**
     * Validate that a discount percentage does not exceed the configured limit.
     *
     * @param float  $discountPercent  The requested discount.
     * @param string $tipo             'item' or 'pedido'.
     * @return void
     * @throws \RuntimeException  If the discount exceeds the maximum configured limit.
     */
    public function validateDiscount(float $discountPercent, string $tipo = 'item'): void
    {
        $maxPolicy = CommercialDiscountPolicy::query()
            ->where('tipo', $tipo)
            ->where('ativo', true)
            ->orderByDesc('percentual_maximo')
            ->first();

        if ($maxPolicy && $discountPercent > (float) $maxPolicy->percentual_maximo) {
            throw new RuntimeException(
                sprintf(
                    'Desconto de %.2f%% excede o limite maximo permitido de %.2f%% para o tipo "%s".',
                    $discountPercent,
                    $maxPolicy->percentual_maximo,
                    $tipo
                )
            );
        }
    }

    /**
     * Apply an order-level discount percentage to a subtotal and return the discount amount.
     *
     * This validates the discount before applying it.
     *
     * @param float $subtotal
     * @param float $discountPercent
     * @return float  The computed discount amount.
     * @throws \RuntimeException
     */
    public function applyOrderDiscount(float $subtotal, float $discountPercent): float
    {
        $this->validateDiscount($discountPercent, 'pedido');

        return round($subtotal * ($discountPercent / 100), 2);
    }
}
