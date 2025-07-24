<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estado';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nome',
        'uf',
        'ibge',
        'pais',
        'ddd',
    ];

    public function cidades(): HasMany{
        return $this->hasMany(Cidade::class);
    }

    use HasFactory;
}
