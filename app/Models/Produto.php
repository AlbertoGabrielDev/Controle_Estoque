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

    public function search(): BelongsToMany
    {
        return $this->belongsToMany(Fornecedor::class, 'estoque', 'id_produto_fk', 'id_fornecedor_fk')
            ->as('estoque')
            ->withPivot([
                'id_estoque',
                'quantidade',
                'localizacao',
                'preco_custo',
                'preco_venda',
                'lote',
                'data_chegada',
                'localizacao',
                'quantidade_aviso',
                'created_at'
            ])->where(function ($query) {
                $query->where(function ($subquery) {
                    if (!is_null(request()->input('lote'))) {
                        $subquery->where('estoque.lote', request()->input('lote'));
                    }
                })->orWhere(function ($subquery) {
                    if (!is_null(request()->input('quantidade'))) {
                        $subquery->where('estoque.quantidade', request()->input('quantidade'));
                    }
                })->orWhere(function ($subquery) {
                    if (!is_null(request()->input('preco_custo'))) {
                        $subquery->where('estoque.preco_custo', request()->input('preco_custo'));
                    }
                });
            });
    }

    public function fornecedores() : BelongsToMany 
    {
        return $this->belongsToMany(Fornecedor::class ,'estoque', 'id_produto_fk', 'id_fornecedor_fk')
        ->as('estoque')
        ->withPivot([
            'id_estoque',
            'quantidade',
            'localizacao',
            'preco_custo',
            'preco_venda',
            'lote',
            'data_chegada',
            'localizacao',
            'quantidade_aviso',
            'created_at'
        ]);
    }

    public function categorias() : BelongsToMany
    {
        return $this->belongsToMany(Categoria::class, 'categoria_produto','id_categoria_fk', 'id_produto_fk');
    }  

    public function marca(){
        return $this->hasMany(Marca::class, 'marca_produto' ,'id_marca_fk', 'id_marca')
        ->as('marca_produto');
    }

    use HasFactory;
}
