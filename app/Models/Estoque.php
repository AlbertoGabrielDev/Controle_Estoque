<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function historicos(): HasMany
    {
        return $this->hasMany(Historico::class, 'id_estoque_fk' , 'id_estoque');
    }

    public function produtos(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'estoque', 'id_estoque' , 'id_produto_fk');
    }

    public function marcas() : BelongsToMany{
        return $this->belongsToMany(Marca::class, 'estoque', 'id_estoque' , 'id_marca_fk');
    }

    public function fornecedores() : BelongsToMany{
        return $this->belongsToMany(Fornecedor::class, 'estoque', 'id_estoque' , 'id_fornecedor_fk');
    }
    use HasFactory;
}
