<?php

namespace Modules\Products\Http\Controllers;


use App\Http\Controllers\Bot\BaseBotController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Products\Models\Produto;

class BotProductController extends BaseBotController
{
    /**
     * Busca produtos ativos.
     *
     * GET /api/bot/products?search=tomate&category_id=1&brand_id=2&code=123
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'search'      => 'nullable|string|min:2|max:100',
            'category_id' => 'nullable|integer',
            'brand_id'    => 'nullable|integer',
            'code'        => 'nullable|string',
            'limit'       => 'nullable|integer|min:1|max:50',
        ]);

        $term = trim((string) $request->input('search'));
        $categoryId = $request->input('category_id');
        $brandId = $request->input('brand_id');
        $code = $request->input('code');
        $limit = $request->input('limit', 20); // Default pagination to 20

        $query = Produto::query()->where('status', 1);

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('nome_produto', 'like', "%{$term}%")
                  ->orWhere('cod_produto', 'like', "%{$term}%")
                  ->orWhere('descricao', 'like', "%{$term}%")
                  ->orWhereHas('categorias', function ($categoryQuery) use ($term) {
                      $categoryQuery->where('nome_categoria', 'like', "%{$term}%");
                  });
            });
        }

        if ($categoryId) {
            $query->whereHas('categorias', function ($q) use ($categoryId) {
                $q->where('categorias.id_categoria', $categoryId);
            });
        }

        if ($brandId) {
            $query->whereHas('marcas', function ($q) use ($brandId) {
                $q->where('marcas.id_marca', $brandId);
            });
        }

        if ($code) {
            $query->where('cod_produto', $code);
        }

        $products = $query
            ->with(['categorias:id_categoria,nome_categoria', 'marcas:id_marca,nome_marca', 'unidadeMedida:id,codigo,descricao'])
            ->limit($limit)
            ->get();

        return $this->responseSuccess([
            'products' => $products->map(fn (Produto $p) => [
                'id'          => $p->id_produto,
                'code'        => $p->cod_produto,
                'name'        => $p->nome_produto,
                'description' => $p->descricao,
                'unit'        => $p->unidadeMedida?->codigo ?? $p->unidade_medida,
                'category'    => $p->categorias->pluck('nome_categoria')->implode(', '),
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
            return $this->responseError('Produto não encontrado', 404);
        }

        return $this->responseSuccess([
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
