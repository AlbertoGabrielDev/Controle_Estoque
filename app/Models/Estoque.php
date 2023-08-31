<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{

    protected  $table = 'estoque';
    protected $primaryKey = 'id_estoque';

    protected $fillable = [
        'id_estoque',
        'quantidade',
        'localizacao',
        'data_entrega',
        'data_cadastro'

    ];

    use HasFactory;
}
