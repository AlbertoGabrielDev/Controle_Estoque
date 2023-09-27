<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{

    protected  $table = 'estoque';
    protected $primaryKey = 'id_estoque';

    protected $fillable = [
        'id_estoque',
        'quantidade',
        'localizacao',
        'data_entrega',
        'data_cadastro',
        'preco_custo',
        'preco_venda',
        'lote',
        'data_chegada',
        'id_produto_fk',
        'id_fornecedor_fk',
        'lote',
        'id_marca_fk',
        'localizacao',
        'created_at',
        'quantidade_aviso'
    ];

    public function historico(): HasOne{
        return $this->hasOne(historico::class);
    }

    use HasFactory;
}
