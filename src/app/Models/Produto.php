<?php

namespace App\Models;

use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Produto extends Model implements Transformable
{ 
    use TransformableTrait;
    use HasFactory;
    use HasStatus;
    protected $table= 'produtos';
    protected $primaryKey = 'id_produto';

    protected $fillable=[
        'cod_produto',
        'nome_produto',
        'descricao',
        'unidade_medida',
        'id_categoria_fk',
        'inf_nutrientes',
        'id_users_fk',
        'qrcode',
        'status'
    ];

    public function fornecedores() : BelongsToMany 
    {
        return $this->belongsToMany(Fornecedor::class ,'estoques', 'id_produto_fk', 'id_fornecedor_fk')
        ->as('estoques')
        ->withPivot([
            'id_estoque',
            'quantidade',
            'localizacao',
            'preco_custo',
            'preco_venda',
            'lote',
            'data_chegada',
            'validade',
            'localizacao',
            'quantidade_aviso',
            'created_at',
            'status'
        ]);
    }

    public function categorias() : BelongsToMany
    {
        return $this->belongsToMany(Categoria::class,  'categoria_produtos', 'id_produto_fk', 'id_categoria_fk');
    }

    public function marcas(): BelongsToMany
    {
        return $this->belongsToMany(Marca::class, 'marca_produtos', 'id_produto_fk', 'id_marca_fk')->as('marca_produto');
    }

    public function vendas()
{
    return $this->hasMany(Venda::class, 'id_produto_fk', 'id_produto');
}
}
