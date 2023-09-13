<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Telefone extends Model
{
    protected $table = 'telefones';
    protected $primaryKey = 'id_telefone';

    protected $fillable = [
        'ddd',
        'telefone',
        'whatsapp',
        'telegram',
        'principal'
    ];

    use HasFactory;
}
