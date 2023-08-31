<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{

    protected $table= 'produto';
    protected $primaryKey = 'cod_produto';

    protected $fillable=[
        'cod_produto',
        'nome_produto',
        'descricao',
        'validade',
        'lote',
        'unidade_medida',
        'preco_produto',
        'id_usuario_fk'
    ];

    use HasFactory;
}
