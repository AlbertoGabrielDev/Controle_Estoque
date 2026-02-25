<?php

namespace Modules\Finance\Models;

use App\Traits\HasDatatableConfig;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CentroCusto extends Model
{
    use HasStatus;
    use HasDatatableConfig;

    protected $table = 'centros_custo';
    protected $statusColumn = 'ativo';

    protected $fillable = [
        'codigo',
        'nome',
        'centro_pai_id',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function centroPai(): BelongsTo
    {
        return $this->belongsTo(CentroCusto::class, 'centro_pai_id');
    }

    public function filhos(): HasMany
    {
        return $this->hasMany(CentroCusto::class, 'centro_pai_id');
    }

    public static function dtColumns(): array
    {
        $t = (new static)->getTable();
        return [
            'id' => ['db' => "{$t}.id", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.codigo", 'label' => 'Código', 'order' => true, 'search' => true],
            'c2' => ['db' => "{$t}.nome", 'label' => 'Nome', 'order' => true, 'search' => true],
            'c3' => [
                'db' => 'cc_pai.codigo',
                'label' => 'Centro Pai',
                'order' => false,
                'search' => true,
                'join' => ["{$t} as cc_pai", 'cc_pai.id', '=', "{$t}.centro_pai_id", 'left'],
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
                    "{$t}.nome",
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
