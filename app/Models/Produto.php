<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Produto extends Model
{

    protected $table= 'produto';
    protected $primaryKey = 'id_produto';

    protected $fillable=[
        'cod_produto',
        'nome_produto',
        'descricao',
        'unidade_medida',
        'validade',
        'id_categoria_fk',
        'id_users_fk',
        'status'
    ];

    // public function fornecedores() : BelongsToMany {
    //     return $this->belongsToMany(Fornecedor::class ,'estoque', 'id_produto_fk', 'id_fornecedor_fk')->withPivot(
    //     'id_estoque',
    //     'quantidade',
    //     'localizacao',
    //     'preco_custo',
    //     'preco_venda',
    //     'lote',
    //     'data_chegada',
    //     'localizacao',
    //     'quantidade_aviso',
    //     'created_at'
    // );
    // }

    public function categorias() : BelongsToMany
    {
        return $this->belongsToMany(Categoria::class, 'categoria_produto','id_categoria_fk', 'id_produto_fk');
    }  

    public function estoques() : BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'produto')
        ->withPivot(
        'cod_produto',
        'nome_produto',
        'descricao',
        'unidade_medida',
        'validade',
        'id_categoria_fk',
        'id_users_fk',
        'status'
            );
    }
    use HasFactory;
}
