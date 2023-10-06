<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Produto extends Model
{
    use HasFactory;
    protected $table= 'produto';
    protected $primaryKey = 'id_produto';

    protected $fillable=[
        'cod_produto',
        'nome_produto',
        'descricao',
        'unidade_medida',
        'validade',
        'id_categoria_fk',
        'inf_nutrientes',
        'id_users_fk',
        'status'
    ];

    public function search(): BelongsToMany
    {
        $query = $this->belongsToMany(Fornecedor::class, 'estoque', 'id_produto_fk', 'id_fornecedor_fk')
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
                'created_at',
                'status'
            ])
            ->join('produto as p', 'estoque.id_produto_fk', '=', 'p.id_produto')
            ->join('marca as m', 'estoque.id_marca_fk', '=', 'm.id_marca')
            ->join('categoria_produto as cp', 'p.id_produto', '=', 'cp.id_produto_fk')
            ->join('categoria as c', 'cp.id_categoria_fk', '=', 'c.id_categoria')
            ->where(function ($query) 
            {
                $query->where(function ($subquery) {
                    if (request()->input('lote')) {
                        $subquery->where('estoque.lote', request()->input('lote'));
                    }
                })->where(function ($subquery) {
                    if (request()->input('quantidade')) {
                        $subquery->where('estoque.quantidade', request()->input('quantidade'));
                    }
                })->where(function ($subquery) {
                    if (request()->input('preco_custo')){
                        $subquery->where('estoque.preco_custo', request()->input('preco_custo'));
                    }
                })->where(function ($subquery){
                    if (request()->input('localizacao')) {
                        $subquery->where('estoque.localizacao', request()->input('localizacao'));
                    }
                })->where(function ($subquery){
                    if (request()->input('preco_venda')) {
                        $subquery->where('estoque.preco_venda', request()->input('preco_venda'));
                    }
                })->where(function ($subquery){
                    if (request()->input('data_chegada')) {
                        $subquery->where('estoque.data_chegada', request()->input('data_chegada'));
                    }
                })->where(function ($subquery){
                    if (request()->input('nome_produto')) {
                        $subquery->where('p.nome_produto', 'like' ,'%' .request()->input('nome_produto') . '%' );
                    }
                })->where(function ($subquery){
                    if (request()->input('nome_marca')) {
                        $subquery->where('m.nome_marca', request()->input('nome_marca'));
                    }
                })->where(function ($subquery){
                    if (request()->input('nome_fornecedor')) {
                        $subquery->where('fornecedor.nome_fornecedor',  request()->input('nome_fornecedor'));
                    }
                })->where(function ($subquery){
                    if (request()->input('nome_categoria')) {
                        $subquery->where('c.nome_categoria', request()->input('nome_categoria'));
                    }
                });
            });

            return $query;
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
            'created_at',
            'status'
        ]);
    }

    public function categorias() : BelongsToMany
    {
        return $this->belongsToMany(Categoria::class,  'categoria_produto', 'id_produto_fk', 'id_categoria_fk');
    }

    public function marca(): BelongsToMany
    {
        return $this->belongsToMany(Marca::class, 'marca_produto' ,'id_marca_fk', 'id_marca')
        ->as('marca_produto');
    }
}
