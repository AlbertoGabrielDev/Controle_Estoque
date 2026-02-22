<?php

namespace App\Models;

use App\Traits\HasDatatableConfig;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Imposto extends Model
{
    use HasStatus;
    use HasDatatableConfig;

    protected $table = 'impostos';
    protected $statusColumn = 'ativo';

    protected $fillable = [
        'codigo',
        'nome',
        'tipo',
        'aliquota_percent',
        'ativo',
    ];

    protected $casts = [
        'aliquota_percent' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class, 'imposto_padrao_id');
    }

    public static function dtColumns(): array
    {
        $t = (new static)->getTable();
        return [
            'id' => ['db' => "{$t}.id", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.codigo", 'label' => 'Código', 'order' => true, 'search' => true],
            'c2' => ['db' => "{$t}.nome", 'label' => 'Nome', 'order' => true, 'search' => true],
            'c3' => ['db' => "{$t}.tipo", 'label' => 'Tipo', 'order' => true, 'search' => true],
            'c4' => ['db' => "{$t}.aliquota_percent", 'label' => 'Alíquota (%)', 'order' => true, 'search' => false],
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
