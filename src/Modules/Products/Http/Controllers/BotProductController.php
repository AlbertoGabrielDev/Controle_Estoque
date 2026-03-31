<?php

namespace Modules\Products\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Products\Models\Produto;

class BotProductController
{
    /**
     * Busca produtos ativos por nome/código.
     *
     * GET /api/bot/products?search=tomate
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'search' => 'required|string|min:2|max:100',
        ]);

        $term = $request->input('search');

        $products = Produto::query()
            ->where('status', 1)
            ->where(function ($q) use ($term) {
                $q->where('nome_produto', 'like', "%{$term}%")
                  ->orWhere('cod_produto', 'like', "%{$term}%")
                  ->orWhere('descricao', 'like', "%{$term}%");
            })
            ->with(['categorias:id_categoria,nome_categoria', 'marcas:id_marca,nome_marca', 'unidadeMedida:id,codigo,descricao'])
            ->limit(20)
            ->get();

        return response()->json([
            'products' => $products->map(fn (Produto $p) => [
                'id'          => $p->id_produto,
                'code'        => $p->cod_produto,
                'name'        => $p->nome_produto,
                'description' => $p->descricao,
                'unit'        => $p->unidadeMedida?->codigo ?? $p->unidade_medida,
                'categories'  => $p->categorias->pluck('nome_categoria')->toArray(),
                'brands'      => $p->marcas->pluck('nome_marca')->toArray(),
            ]),
            'count' => $products->count(),
        ]);
    }

    /**
     * Detalhes de um produto específico.
     *
     * GET /api/bot/products/{id}
     */
    public function show(int $id): JsonResponse
    {
        $product = Produto::query()
            ->where('status', 1)
            ->with(['categorias:id_categoria,nome_categoria', 'marcas:id_marca,nome_marca', 'unidadeMedida:id,codigo,descricao'])
            ->find($id);

        if (! $product) {
            return response()->json(['error' => 'Produto não encontrado'], 404);
        }

        return response()->json([
            'product' => [
                'id'          => $product->id_produto,
                'code'        => $product->cod_produto,
                'name'        => $product->nome_produto,
                'description' => $product->descricao,
                'unit'        => $product->unidadeMedida?->codigo ?? $product->unidade_medida,
                'nutrition'   => $product->inf_nutriente,
                'categories'  => $product->categorias->pluck('nome_categoria')->toArray(),
                'brands'      => $product->marcas->pluck('nome_marca')->toArray(),
            ],
        ]);
    }
}
