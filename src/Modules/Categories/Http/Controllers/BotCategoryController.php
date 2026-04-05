<?php

namespace Modules\Categories\Http\Controllers;

use App\Http\Controllers\Bot\BaseBotController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Categories\Models\Categoria;

class BotCategoryController extends BaseBotController
{
    /**
     * Busca categorias ativas.
     *
     * GET /api/bot/categories?search=fruta&limit=10
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'search' => 'nullable|string|min:2|max:100',
            'limit'  => 'nullable|integer|min:1|max:50',
        ]);

        $term = trim((string) $request->input('search'));
        $limit = $request->input('limit', 20);

        $query = Categoria::query()->where('status', 1);

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('nome_categoria', 'like', "%{$term}%")
                  ->orWhere('codigo', 'like', "%{$term}%");
            });
        }

        $categories = $query->limit($limit)->get();

        return $this->responseSuccess([
            'categories' => $categories->map(fn (Categoria $c) => [
                'id'    => $c->id_categoria,
                'code'  => $c->codigo,
                'name'  => $c->nome_categoria,
                'type'  => $c->tipo,
            ]),
            'count' => $categories->count(),
        ]);
    }

    /**
     * Detalhes de uma categoria específica.
     *
     * GET /api/bot/categories/{id}
     */
    public function show(int $id): JsonResponse
    {
        $category = Categoria::query()
            ->where('status', 1)
            ->find($id);

        if (! $category) {
            return $this->responseError('Categoria não encontrada', 404);
        }

        return $this->responseSuccess([
            'category' => [
                'id'    => $category->id_categoria,
                'code'  => $category->codigo,
                'name'  => $category->nome_categoria,
                'type'  => $category->tipo,
            ],
        ]);
    }
}
