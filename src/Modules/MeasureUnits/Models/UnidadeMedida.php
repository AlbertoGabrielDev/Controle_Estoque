<?php

namespace Modules\MeasureUnits\Models;

use App\Traits\HasDatatableConfig;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadeMedida extends Model
{
    use HasStatus;
    use HasDatatableConfig;

    protected $table = 'unidades_medida';
    protected $statusColumn = 'ativo';

    protected $fillable = [
        'codigo',
        'descricao',
        'fator_base',
        'unidade_base_id',
        'ativo',
    ];

    protected $casts = [
        'fator_base' => 'decimal:6',
        'ativo' => 'boolean',
    ];

    public function unidadeBase(): BelongsTo
    {
        return $this->belongsTo(self::class, 'unidade_base_id');
    }

    public function derivadas(): HasMany
    {
        return $this->hasMany(self::class, 'unidade_base_id');
    }

    public static function dtColumns(): array
    {
        $t = (new static)->getTable();

        return [
            'id' => ['db' => "{$t}.id", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.codigo", 'label' => 'Codigo', 'order' => true, 'search' => true],
            'c2' => ['db' => "{$t}.descricao", 'label' => 'Descricao', 'order' => true, 'search' => true],
            'c3' => ['db' => "{$t}.fator_base", 'label' => 'Fator Base', 'order' => true, 'search' => false],
            'c4' => [
                'db' => 'ub.codigo',
                'label' => 'Unidade Base',
                'order' => false,
                'search' => true,
                'join' => ["{$t} as ub", 'ub.id', '=', "{$t}.unidade_base_id", 'left'],
            ],
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
                    "{$t}.descricao",
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
