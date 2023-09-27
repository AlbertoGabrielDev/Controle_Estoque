<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historico extends Model
{
    protected $table = 'historico';
    protected $primaryKey = 'id_historico';

    protected $fillable = [
        'quantidade_diminuida',
        'id_estoque_fk'
    ];
    use HasFactory;
}
