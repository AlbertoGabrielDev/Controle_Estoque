<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    protected $table = 'cidade';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nome',
        'uf',
        'ibge',
        'id_estado_fk',
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    use HasFactory;
}
