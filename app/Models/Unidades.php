<?php

namespace App\Models;

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

}
