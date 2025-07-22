<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaProduto extends Model
{
    protected $table = 'categoria_produto';
    protected $primaryKey= 'id_categoria_produto';

    protected $fillable = [
        'id_produto_fk',
        'id_categoria_fk'
    ];

    use HasFactory;
}
