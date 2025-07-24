<?php

namespace App\Models;

use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Marca extends Model
{
    use HasStatus;
    protected $table = 'marcas';
    protected $primaryKey = 'id_marca';

    protected $fillable = [
        'nome_marca',
        'id_users_fk'
    ];


    public function produto(): BelongsToMany{
        return $this->belongsToMany(Produto::class, 'marca_produtos' ,'id_marca_fk', 'id_marca')
        ->as('marca_produto');
    }

    public function estoques(): BelongsToMany{
        return $this->belongsToMany(Estoque::class, 'marcas' ,'id_marca');
    }

    use HasFactory;
}
