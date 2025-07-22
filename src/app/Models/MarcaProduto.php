<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarcaProduto extends Model
{
    protected $table = 'marca_produto';
    protected $primaryKey = 'id_marca_produto';

    protected $fillable = [
        'id_produto_fk',
        'id_marca_fk'
    ];

    use HasFactory;
}
