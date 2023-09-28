<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marca';
    protected $primaryKey = 'id_marca';

    protected $fillable = [
        'nome_marca',
        'id_users_fk'
    ];

    public function produto(): BelongsToMany{
        return $this->belongsToMany(Produto::class, 'marca_produto' ,'id_marca_fk', 'id_marca')
        ->as('marca_produto');
    }

    public function estoques(): BelongsToMany{
        return $this->belongsToMany(Estoque::class, 'marca' ,'id_marca');
    }

    use HasFactory;
}
