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
        'status',
        'id_telefone_fk'
    ];

    public function produto(): BelongsToMany{
        return $this->belongsToMany(Produto::class, 'estoque', 'id_fornecedor_fk', 'id_produto_fk');
    }
    use HasFactory;
}
