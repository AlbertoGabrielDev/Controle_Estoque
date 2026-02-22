<?php

namespace App\Models;

use App\Traits\HasDatatableConfig;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    use HasStatus;
    use HasDatatableConfig;

    protected $table = 'itens';
    protected $statusColumn = 'ativo';

    protected $fillable = [
        'sku',
        'nome',
        'tipo',
        'categoria_id',
        'unidade_medida_id',
        'descricao',
        'custo',
        'preco_base',
        'peso_kg',
        'volume_m3',
        'controla_estoque',
        'ativo',
    ];

    protected $casts = [
        'custo' => 'decimal:2',
        'preco_base' => 'decimal:2',
        'peso_kg' => 'decimal:3',
        'volume_m3' => 'decimal:6',
        'controla_estoque' => 'boolean',
        'ativo' => 'boolean',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id_categoria');
    }

    public function unidadeMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadeMedida::class, 'unidade_medida_id');
    }

    public function tabelasPreco(): BelongsToMany
    {
        return $this->belongsToMany(TabelaPreco::class, 'tabela_preco_itens', 'item_id', 'tabela_preco_id')
            ->withPivot(['preco', 'desconto_percent', 'quantidade_minima'])
            ->withTimestamps();
    }

    public static function dtColumns(): array
    {
        $t = (new static)->getTable();
        $joinCategoria = ['categorias', 'categorias.id_categoria', '=', "{$t}.categoria_id", 'left'];
        $joinUnidade = ['unidades_medida', 'unidades_medida.id', '=', "{$t}.unidade_medida_id", 'left'];

        return [
            'id' => ['db' => "{$t}.id", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.sku", 'label' => 'SKU', 'order' => true, 'search' => true],
            'c2' => ['db' => "{$t}.nome", 'label' => 'Nome', 'order' => true, 'search' => true],
            'c3' => ['db' => "{$t}.tipo", 'label' => 'Tipo', 'order' => true, 'search' => true],
            'c4' => [
                'db' => 'categorias.nome_categoria',
                'label' => 'Categoria',
                'order' => false,
                'search' => true,
                'join' => $joinCategoria,
            ],
            'c5' => [
                'db' => 'unidades_medida.codigo',
                'label' => 'Unidade',
                'order' => false,
                'search' => true,
                'join' => $joinUnidade,
            ],
            'c6' => ['db' => "{$t}.preco_base", 'label' => 'Preço Base', 'order' => true, 'search' => false],
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
                    "{$t}.sku",
                    "{$t}.nome",
                ],
            ],
            'tipo' => [
                'type' => 'select',
                'column' => "{$t}.tipo",
                'operator' => '=',
                'nullable' => true,
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
