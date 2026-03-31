<?php

namespace Modules\Stock\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Stock\Models\Estoque;

class BotStockController
{
    /**
     * Consulta estoque de um produto específico.
     *
     * GET /api/bot/stock?product_id=1
     */
    public function byProduct(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:produtos,id_produto',
        ]);

        $items = Estoque::query()
            ->where('id_produto_fk', $request->input('product_id'))
            ->where('status', 1)
            ->where('quantidade', '>', 0)
            ->with(['produtos:id_produto,nome_produto,cod_produto', 'marcas:id_marca,nome_marca'])
            ->get();

        return response()->json([
            'stock' => $items->map(fn (Estoque $e) => [
                'product_name' => $e->produtos?->nome_produto,
                'product_code' => $e->produtos?->cod_produto,
                'quantity'     => (float) $e->quantidade,
                'sell_price'   => (float) $e->preco_venda,
                'brand'        => $e->marcas?->nome_marca,
                'batch'        => $e->lote,
                'location'     => $e->localizacao,
                'expiry_date'  => $e->validade?->format('Y-m-d'),
            ]),
            'total_quantity' => $items->sum('quantidade'),
        ]);
    }

    /**
     * Busca disponibilidade geral por termo.
     *
     * GET /api/bot/stock/availability?search=tomate
     */
    public function availability(Request $request): JsonResponse
    {
        $request->validate([
            'search' => 'required|string|min:2|max:100',
        ]);

        $term = $request->input('search');

        $items = Estoque::query()
            ->where('status', 1)
            ->where('quantidade', '>', 0)
            ->whereHas('produtos', function ($q) use ($term) {
                $q->where('status', 1)
                  ->where(function ($sub) use ($term) {
                      $sub->where('nome_produto', 'like', "%{$term}%")
                          ->orWhere('cod_produto', 'like', "%{$term}%");
                  });
            })
            ->with(['produtos:id_produto,nome_produto,cod_produto', 'marcas:id_marca,nome_marca'])
            ->limit(20)
            ->get();

        return response()->json([
            'available' => $items->map(fn (Estoque $e) => [
                'product_name' => $e->produtos?->nome_produto,
                'product_code' => $e->produtos?->cod_produto,
                'quantity'     => (float) $e->quantidade,
                'sell_price'   => (float) $e->preco_venda,
                'brand'        => $e->marcas?->nome_marca,
                'expiry_date'  => $e->validade?->format('Y-m-d'),
            ]),
            'count' => $items->count(),
        ]);
    }
}
