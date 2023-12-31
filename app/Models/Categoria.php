<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categoria';
    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'nome_categoria',
        'id_users_fk',
        'imagem',
        'status'
    ];

    public function produtos() : BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'categoria_produto','id_categoria_fk' ,'id_produto_fk');
    }
}
