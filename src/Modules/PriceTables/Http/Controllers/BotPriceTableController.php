<?php

namespace Modules\PriceTables\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\PriceTables\Models\TabelaPreco;

class BotPriceTableController
{
    /**
     * Tabela de preço ativa com seus produtos.
     *
     * GET /api/bot/price-tables/active
     */
    public function active(): JsonResponse
    {
        $table = TabelaPreco::query()
            ->where('ativo', true)
            ->where(function ($q) {
                $q->whereNull('fim_vigencia')
                  ->orWhere('fim_vigencia', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('inicio_vigencia')
                  ->orWhere('inicio_vigencia', '<=', now());
            })
            ->with(['produtos' => function ($q) {
                $q->where('status', 1)->limit(50);
            }])
            ->first();

        if (! $table) {
            return response()->json([
                'found'       => false,
                'price_table' => null,
            ]);
        }

        return response()->json([
            'found'       => true,
            'price_table' => [
                'name'     => $table->nome,
                'code'     => $table->codigo,
                'currency' => $table->moeda,
                'valid_from' => $table->inicio_vigencia?->format('Y-m-d'),
                'valid_to'   => $table->fim_vigencia?->format('Y-m-d'),
                'products' => $table->produtos->map(fn ($p) => [
                    'name'             => $p->nome_produto,
                    'code'             => $p->cod_produto,
                    'price'            => (float) $p->pivot->preco,
                    'discount_percent' => (float) ($p->pivot->desconto_percent ?? 0),
                    'min_quantity'     => (float) ($p->pivot->quantidade_minima ?? 0),
                ]),
            ],
        ]);
    }

    /**
     * Cotação: preço para uma lista de produtos.
     *
     * GET /api/bot/price-tables/quote?items=[{"product_id":1,"quantity":10}]
     */
    public function quote(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|string',
        ]);

        $items = json_decode($request->input('items'), true);

        if (! is_array($items) || count($items) === 0) {
            return response()->json(['error' => 'Lista de itens inválida'], 422);
        }

        $table = TabelaPreco::query()
            ->where('ativo', true)
            ->where(function ($q) {
                $q->whereNull('fim_vigencia')
                  ->orWhere('fim_vigencia', '>=', now());
            })
            ->first();

        if (! $table) {
            return response()->json(['error' => 'Nenhuma tabela de preço ativa'], 404);
        }

        $productIds = collect($items)->pluck('product_id')->toArray();
        $tableProducts = $table->produtos()
            ->whereIn('produtos.id_produto', $productIds)
            ->get()
            ->keyBy('id_produto');

        $quotedItems = [];
        $total = 0;

        foreach ($items as $item) {
            $pid = $item['product_id'] ?? null;
            $qty = (float) ($item['quantity'] ?? 0);

            $product = $tableProducts->get($pid);

            if (! $product) {
                $quotedItems[] = [
                    'product_id' => $pid,
                    'found'      => false,
                    'quantity'   => $qty,
                    'unit_price' => 0,
                    'subtotal'   => 0,
                ];
                continue;
            }

            $price = (float) $product->pivot->preco;
            $discount = (float) ($product->pivot->desconto_percent ?? 0);
            $finalPrice = $price * (1 - $discount / 100);
            $subtotal = round($finalPrice * $qty, 2);
            $total += $subtotal;

            $quotedItems[] = [
                'product_id'   => $pid,
                'product_name' => $product->nome_produto,
                'found'        => true,
                'quantity'     => $qty,
                'unit_price'   => round($finalPrice, 2),
                'subtotal'     => $subtotal,
            ];
        }

        return response()->json([
            'price_table' => $table->nome,
            'currency'    => $table->moeda,
            'items'       => $quotedItems,
            'total'       => round($total, 2),
        ]);
    }
}
