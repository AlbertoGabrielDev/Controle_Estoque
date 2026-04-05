<?php

namespace Modules\Commercial\Models;

use App\Traits\HasDatatableConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommercialDiscountPolicy extends Model
{
    use HasFactory;
    use HasDatatableConfig;

    protected $table = 'commercial_discount_policies';

    protected $fillable = [
        'nome',
        'tipo',
        'percentual_maximo',
        'ativo',
    ];

    protected $casts = [
        'percentual_maximo' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    /**
     * Get DataTables columns configuration.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function dtColumns(): array
    {
        $t = (new static())->getTable();

        return [
            'id' => ['db' => "{$t}.id", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.nome", 'label' => 'Nome', 'order' => true, 'search' => true],
            'c2' => ['db' => "{$t}.tipo", 'label' => 'Tipo', 'order' => true, 'search' => true],
            'c3' => ['db' => "{$t}.percentual_maximo", 'label' => '% Maximo', 'order' => true, 'search' => false],
            'st' => ['db' => "{$t}.ativo", 'label' => 'Ativo', 'order' => true, 'search' => false],
            'acoes' => ['computed' => true],
        ];
    }

    /**
     * Get DataTables filters configuration.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function dtFilters(): array
    {
        $t = (new static())->getTable();

        return [
            'q' => [
                'type' => 'text',
                'columns' => ["{$t}.nome"],
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
