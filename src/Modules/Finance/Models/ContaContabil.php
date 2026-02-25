<?php

namespace Modules\Finance\Models;

use App\Traits\HasDatatableConfig;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContaContabil extends Model
{
    use HasStatus;
    use HasDatatableConfig;

    protected $table = 'contas_contabeis';
    protected $statusColumn = 'ativo';

    protected $fillable = [
        'codigo',
        'nome',
        'tipo',
        'conta_pai_id',
        'aceita_lancamento',
        'ativo',
    ];

    protected $casts = [
        'aceita_lancamento' => 'boolean',
        'ativo' => 'boolean',
    ];

    public function contaPai(): BelongsTo
    {
        return $this->belongsTo(ContaContabil::class, 'conta_pai_id');
    }

    public function filhas(): HasMany
    {
        return $this->hasMany(ContaContabil::class, 'conta_pai_id');
    }

    public static function dtColumns(): array
    {
        $t = (new static)->getTable();
        return [
            'id' => ['db' => "{$t}.id", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.codigo", 'label' => 'Código', 'order' => true, 'search' => true],
            'c2' => ['db' => "{$t}.nome", 'label' => 'Nome', 'order' => true, 'search' => true],
            'c3' => ['db' => "{$t}.tipo", 'label' => 'Tipo', 'order' => true, 'search' => true],
            'c4' => [
                'db' => 'cc_pai.codigo',
                'label' => 'Conta Pai',
                'order' => false,
                'search' => true,
                'join' => ["{$t} as cc_pai", 'cc_pai.id', '=', "{$t}.conta_pai_id", 'left'],
            ],
            'c5' => ['db' => "{$t}.aceita_lancamento", 'label' => 'Aceita Lançamento', 'order' => true, 'search' => false],
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
                    "{$t}.tipo",
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
