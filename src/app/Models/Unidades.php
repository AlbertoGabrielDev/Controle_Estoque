<?php

namespace App\Models;

use App\Traits\HasDatatableConfig;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Unidades.
 *
 * @package namespace App\Entities;
 */
class Unidades extends Model implements Transformable
{
    use TransformableTrait;
    use HasStatus;
    use HasDatatableConfig;
    protected $table= 'unidades';
    protected $primaryKey = 'id_unidade';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome',
        'status',
        'id_users_fk',
        'created_at',
        'update_at'
    ];

    public static function dtColumns(): array
    {
        $t = (new static)->getTable();
        return [
            'id' => ['db' => "{$t}.id_unidade", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.nome", 'label' => 'Unidade', 'order' => true, 'search' => true],
            'st' => ['db' => "{$t}.status", 'label' => 'Status', 'order' => true, 'search' => false],
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
                    "{$t}.nome",
                ],
            ],
            'status' => [
                'type' => 'select',
                'column' => "{$t}.status",
                'cast' => 'int',
                'operator' => '=',
                'nullable' => true,
            ],
        ];
    }
}
