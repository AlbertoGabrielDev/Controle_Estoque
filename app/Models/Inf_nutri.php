<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inf_nutri extends Model
{
    protected $table = 'inf_nutri';
    protected $primaryKey = 'id_inf_nutri';

    protected $fillable = [
        'id_nutri',
        'carboidrato',
        'proteina',
        'sodio',
        'valor_energetico'
    ];
    use HasFactory;
}
