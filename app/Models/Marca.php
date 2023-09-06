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

    public function produto(){
        return $this->hasMany(Produto::class, 'id_marca_fk', 'id_marca');
    }

    use HasFactory;
}
