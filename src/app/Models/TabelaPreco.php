<?php

namespace App\Models;

use App\Traits\HasDatatableConfig;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Products\Models\Produto;

class TabelaPreco extends Model
{
    use HasStatus;
    use HasDatatableConfig;

    protected $table = 'tabelas_preco';
    protected $statusColumn = 'ativo';

    protected $fillable = [
        'codigo',
        'nome',
        'tipo_alvo',
        'moeda',
        'inicio_vigencia',
        'fim_vigencia',
        'ativo',
    ];

    protected $casts = [
        'inicio_vigencia' => 'date',
        'fim_vigencia' => 'date',
        'ativo' => 'boolean',
    ];

    public function itens(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'tabela_preco_itens', 'tabela_preco_id', 'item_id')
            ->withPivot(['preco', 'desconto_percent', 'quantidade_minima', 'marca_id', 'fornecedor_id'])
            ->withTimestamps();
    }

    public function produtos(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'tabela_preco_itens', 'tabela_preco_id', 'produto_id', 'id', 'id_produto')
            ->withPivot(['preco', 'desconto_percent', 'quantidade_minima', 'marca_id', 'fornecedor_id'])
            ->withTimestamps();
    }

    public static function dtColumns(): array
    {
        $t = (new static)->getTable();
        return [
            'id' => ['db' => "{$t}.id", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.codigo", 'label' => 'Código', 'order' => true, 'search' => true],
            'c2' => ['db' => "{$t}.nome", 'label' => 'Nome', 'order' => true, 'search' => true],
            'c3' => ['db' => "{$t}.tipo_alvo", 'label' => 'Tipo', 'order' => true, 'search' => true],
            'c4' => ['db' => "{$t}.moeda", 'label' => 'Moeda', 'order' => true, 'search' => true],
            'c5' => ['db' => "{$t}.inicio_vigencia", 'label' => 'Início', 'order' => true, 'search' => false],
            'c6' => ['db' => "{$t}.fim_vigencia", 'label' => 'Fim', 'order' => true, 'search' => false],
            'st' => ['db' => "{$t}.ativo", 'label' => 'Ativo', 'order' => true, 'search' => false],
            'acoes' => ['computed' => true],
        ];
    }

    public static function dtFilters(): array
    {
        $t = (new static)->getTable();
        return [
            'q' => [
                'type' => 'text',
                'columns' => [
                    "{$t}.codigo",
                    "{$t}.nome",
                    "{$t}.tipo_alvo",
                ],
            ],
            'ativo' => [
                'type' => 'select',
                'column' => "{$t}.ativo",
                'cast' => 'int',
                'operator' => '=',
                'nullable' => true,
            ],
        ];
    }
}
