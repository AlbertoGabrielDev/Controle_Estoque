<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{

    protected $table= 'usuario';
    protected $primaryKey = 'id_usuario';

    protected $fillable=[
        'nome_usuario',
        'login',
        'senha'
    ];

    use HasFactory;
}
