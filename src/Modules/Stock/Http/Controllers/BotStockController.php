<?php

namespace Modules\Stock\Http\Controllers;


use App\Http\Controllers\Bot\BaseBotController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Stock\Models\Estoque;

class BotStockController extends BaseBotController
{
    /**
     * Busca estoque e disponibilidade de produtos.
     * Unifica as rotas antigas de byProduct e availability.
     *
     * GET /api/bot/stock?search=tomate&product_id=1&batch=L123&min_quantity=10
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'search'       => 'nullable|string|min:2|max:100',
            'product_id'   => 'nullable|integer',
            'batch'        => 'nullable|string',
            'min_quantity' => 'nullable|numeric|min:0',
            'limit'        => 'nullable|integer|min:1|max:50',
        ]);

        $term = trim((string) $request->input('search'));
        $productId = $request->input('product_id');
        $batch = $request->input('batch');
        $minQuantity = (float) $request->input('min_quantity', 0.01); // Exibe apenas itens com > 0
        $limit = $request->input('limit', 20);

        $query = Estoque::query()
            ->where('status', 1)
            ->where('quantidade', '>=', $minQuantity);

        if ($productId) {
            $query->where('id_produto_fk', $productId);
        }

        if ($batch) {
            $query->where('lote', $batch);
        }

        if ($term !== '') {
            $query->whereHas('produtos', function ($q) use ($term) {
                $q->where('status', 1)
                  ->where(function ($sub) use ($term) {
                      $sub->where('nome_produto', 'like', "%{$term}%")
                          ->orWhere('cod_produto', 'like', "%{$term}%")
                          ->orWhere('descricao', 'like', "%{$term}%")
                          ->orWhereHas('categorias', function ($categoryQuery) use ($term) {
                              $categoryQuery->where('nome_categoria', 'like', "%{$term}%");
                          });
                  });
            });
        }

        $items = $query
            ->with(['produtos:id_produto,nome_produto,cod_produto', 'marcas:id_marca,nome_marca'])
            ->limit($limit)
            ->get();

        return $this->responseSuccess([
            'stock' => $items->map(fn (Estoque $e) => [
                'product_name' => $e->produtos?->nome_produto,
                'product_code' => $e->produtos?->cod_produto,
                'quantity'     => (float) $e->quantidade,
                'sell_price'   => $this->formatCurrency($e->preco_venda),
                'brand'        => $e->marcas?->nome_marca,
                'batch'        => $e->lote,
                'location'     => $e->localizacao,
                'expiry_date'  => $e->validade?->format('Y-m-d'),
            ]),
            'count' => $items->count(),
            'total_quantity' => $items->sum('quantidade'),
        ]);
    }

    /**
     * Detalhes de um lote de estoque específico (opcional para manter a assinatura show, se referir ao ID único de estoque).
     *
     * GET /api/bot/stock/{id}
     */
    public function show(int $id): JsonResponse
    {
        $estoque = Estoque::query()
            ->where('status', 1)
            ->with(['produtos:id_produto,nome_produto,cod_produto', 'marcas:id_marca,nome_marca'])
            ->find($id);

        if (! $estoque) {
            return $this->responseError('Estoque não encontrado', 404);
        }

        return $this->responseSuccess([
            'stock_item' => [
                'id'           => $estoque->id_estoque,
                'product_name' => $estoque->produtos?->nome_produto,
                'product_code' => $estoque->produtos?->cod_produto,
                'quantity'     => (float) $estoque->quantidade,
                'sell_price'   => $this->formatCurrency($estoque->preco_venda),
                'brand'        => $estoque->marcas?->nome_marca,
                'batch'        => $estoque->lote,
                'location'     => $estoque->localizacao,
                'expiry_date'  => $estoque->validade?->format('Y-m-d'),
            ],
        ]);
    }
}
