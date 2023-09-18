<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Produto extends Model
{

    protected $table= 'produto';
    protected $primaryKey = 'id_produto';

    protected $fillable=[
        'cod_produto',
        'nome_produto',
        'descricao',
        'unidade_medida',
        'validade',
        'id_categoria_fk',
        'id_users_fk'
    ];

    public function categoria() : BelongsToMany
    {
        return $this->belongsToMany(Categoria::class, 'categoria_produto','id_categoria_fk', 'id_produto_fk');
    }
    use HasFactory;
}
