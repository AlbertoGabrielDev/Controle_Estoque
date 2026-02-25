<?php

namespace Modules\Products\Repositories;

use App\Models\Categoria;
use App\Models\Item;
use App\Models\UnidadeMedida;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Products\Models\Produto;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class ProdutoRepositoryEloquent extends BaseRepository implements ProdutoRepository
{
    public function model()
    {
        return Produto::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
        parent::boot();
    }

    public function modelInstance(): Produto
    {
        /** @var Produto $model */
        $model = $this->model;

        return $model;
    }

    public function cadastroPayload(): array
    {
        return [
            'categorias' => Categoria::query()
                ->select('id_categoria', 'nome_categoria')
                ->orderBy('nome_categoria')
                ->get(),
            'unidades' => UnidadeMedida::query()
                ->select('id', 'codigo', 'descricao')
                ->orderBy('codigo')
                ->get(),
            'itens' => Item::query()
                ->select('id', 'sku', 'nome')
                ->orderBy('nome')
                ->get(),
        ];
    }

    public function createProduto(array $payload): Produto
    {
        $categoriaId = (int) ($payload['id_categoria_fk'] ?? 0);
        $unidadeId = $payload['unidade_medida_id'] ?? null;
        $unidadeCodigo = UnidadeMedida::query()->find($unidadeId)?->codigo;

        /** @var Produto $produto */
        $produto = $this->create([
            'nome_produto' => $payload['nome_produto'],
            'cod_produto' => $payload['cod_produto'],
            'descricao' => $payload['descricao'] ?? null,
            'unidade_medida' => $unidadeCodigo,
            'unidade_medida_id' => $unidadeId,
            'item_id' => $payload['item_id'] ?? null,
            'inf_nutriente' => $payload['inf_nutriente'] ?? null,
            'id_users_fk' => $payload['id_users_fk'] ?? null,
        ]);

        if ($categoriaId > 0) {
            $produto->categorias()->sync([$categoriaId]);
        }

        return $produto;
    }

    public function editarPayload(int $produtoId): array
    {
        $produto = Produto::query()
            ->with('categorias:id_categoria,nome_categoria')
            ->findOrFail($produtoId);

        return [
            'produto' => $produto,
            'categorias' => Categoria::query()
                ->select('id_categoria', 'nome_categoria')
                ->orderBy('nome_categoria')
                ->get(),
            'unidades' => UnidadeMedida::query()
                ->select('id', 'codigo', 'descricao')
                ->orderBy('codigo')
                ->get(),
            'itens' => Item::query()
                ->select('id', 'sku', 'nome')
                ->orderBy('nome')
                ->get(),
            'categoriaSelecionada' => optional($produto->categorias->first())->id_categoria,
        ];
    }

    public function updateProduto(int $produtoId, array $payload): Produto
    {
        /** @var Produto $produto */
        $produto = Produto::query()->findOrFail($produtoId);
        $unidadeId = $payload['unidade_medida_id'] ?? null;
        $unidadeCodigo = UnidadeMedida::query()->find($unidadeId)?->codigo;

        $produto->update([
            'cod_produto' => $payload['cod_produto'],
            'nome_produto' => $payload['nome_produto'],
            'descricao' => $payload['descricao'] ?? null,
            'unidade_medida' => $unidadeCodigo,
            'unidade_medida_id' => $unidadeId,
            'item_id' => $payload['item_id'] ?? null,
            'inf_nutriente' => $payload['inf_nutriente'] ?? null,
        ]);

        if (array_key_exists('id_categoria_fk', $payload) && (int) $payload['id_categoria_fk'] > 0) {
            $produto->categorias()->sync([(int) $payload['id_categoria_fk']]);
        }

        return $produto->load('categorias:id_categoria,nome_categoria');
    }

    public function searchAtivos(string $q, int $limit = 25): Collection
    {
        $vendaAgg = DB::table('estoques')
            ->select(
                'id_produto_fk',
                DB::raw('MAX(preco_venda) AS preco_venda'),
                DB::raw('SUM(quantidade) AS qtd_disponivel')
            )
            ->where('status', 1)
            ->groupBy('id_produto_fk');

        $query = DB::table('produtos as p')
            ->leftJoinSub($vendaAgg, 's', fn($j) => $j->on('s.id_produto_fk', '=', 'p.id_produto'))
            ->where('p.status', 1)
            ->select(
                'p.cod_produto',
                'p.nome_produto',
                DB::raw('COALESCE(s.preco_venda, 0) AS preco_venda'),
                DB::raw('COALESCE(s.qtd_disponivel, 0) AS qtd_disponivel')
            );

        $needle = trim($q);
        if ($needle !== '') {
            $needle = preg_replace('/\s+/u', ' ', mb_strtolower($needle, 'UTF-8')) ?? '';
            $terms = array_values(array_filter(
                preg_split('/\s+/u', $needle, -1, PREG_SPLIT_NO_EMPTY) ?: [],
                fn($term) => mb_strlen($term, 'UTF-8') > 1
            ));

            $collation = 'utf8mb4_unicode_ci';
            $query->where(function ($w) use ($terms, $collation) {
                foreach ($terms as $term) {
                    $w->where(function ($w2) use ($term, $collation) {
                        $w2->whereRaw("p.cod_produto COLLATE {$collation} LIKE ?", ["%{$term}%"])
                            ->orWhereRaw("p.nome_produto COLLATE {$collation} LIKE ?", ["%{$term}%"]);
                    });
                }
            });

            $orderSql = "CASE
                WHEN p.nome_produto COLLATE {$collation} = ? THEN 400
                WHEN p.nome_produto COLLATE {$collation} LIKE ? THEN 300
                WHEN p.nome_produto COLLATE {$collation} LIKE ? THEN 200
                WHEN p.cod_produto COLLATE {$collation} LIKE ? THEN 150
                ELSE 0 END DESC, p.nome_produto ASC";
            $query->orderByRaw($orderSql, [
                $needle,
                "{$needle}%",
                "%{$needle}%",
                "%{$needle}%",
            ]);
        } else {
            $query->orderBy('p.nome_produto');
        }

        return $query->limit($limit)->get()->map(function ($row) {
            $row->preco_venda = (float) $row->preco_venda;
            $row->qtd_disponivel = (int) $row->qtd_disponivel;

            return $row;
        });
    }
}
