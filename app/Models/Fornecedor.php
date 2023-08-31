<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    protected $table = 'fornecedor';
    protected $primaryKey = 'id_fornecedor';

    protected $fillable = [
        'id_fornecedor',
        'nome_fornecedor',
        'preco_fornecedor'
    ];
    use HasFactory;
}
