<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marca';
    protected $primaryKey = 'id_marca';

    protected $fillable = [
        'id_marca',
        'marca'
    ];

    public function produto(){
        return $this->hasMany(Produto::class, 'id_marca_fk', 'id_marca');
    }

    use HasFactory;
}
