<?php

namespace Modules\Products\Repositories;

use Illuminate\Support\Collection;
use Modules\Products\Models\Produto;

interface ProdutoRepository
{
    public function modelInstance(): Produto;

    /**
     * @return array<string, mixed>
     */
    public function cadastroPayload(): array;

    public function createProduto(array $payload): Produto;

    /**
     * @return array<string, mixed>
     */
    public function editarPayload(int $produtoId): array;

    public function updateProduto(int $produtoId, array $payload): Produto;

    public function searchAtivos(string $q, int $limit = 25): Collection;
}
