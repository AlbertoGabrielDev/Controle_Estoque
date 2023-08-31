<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{

    protected $table = 'categoria';
    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'categoria'
    ];

    public function produtos()
    {
        return $this->hasMany(Produto::class, 'id_categoria_fk', 'id_categoria');
    }
    
    use HasFactory;
}
