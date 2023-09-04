<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estado';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_fornecedor',
        'nome',
        'uf',
        'ibge',
        'pais',
        'ddd',
    ];

    use HasFactory;
}
