<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Fornecedor extends Model
{
    protected $table = 'fornecedor';
    protected $primaryKey = 'id_fornecedor';

    protected $fillable = [
        'id_fornecedor',
        'nome_fornecedor',
        'cnpj',
        'cep',
        'logradouro',
        'bairro',
        'numero_casa',
        'email',
        'cidade',
        'uf',
        'id_users_fk',
        'id_cidade_fk',
        'status'
    ];

    public function produtos(): BelongsToMany{
        return $this->belongsToMany(Produto::class, 'estoque', 'id_fornecedor_fk', 'id_produto_fk');
    }

    public function estoques(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'estoque', 'id_fornecedor_fk', 'id_produto_fk')
            ->withPivot([  
                'id_estoque',
                'quantidade',
                'localizacao',
                'preco_custo',
                'preco_venda',
                'lote',
                'data_chegada',
                'validade',
                'quantidade_aviso',
                'created_at',
                'status'
            ]);
    }

    public function telefones(): HasMany
    {
        return $this->hasMany(Telefone::class, 'id_fornecedor_fk');
    }


    use HasFactory;
}
