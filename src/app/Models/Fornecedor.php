<?php

namespace App\Models;

use App\Traits\HasDatatableConfig;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Fornecedor extends Model
{
    use HasStatus;
    use HasDatatableConfig;
    protected $table = 'fornecedores';
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
        return $this->belongsToMany(Produto::class, 'estoques', 'id_fornecedor_fk', 'id_produto_fk');
    }

    public function estoques(): BelongsToMany{
        return $this->belongsToMany(Estoque::class, 'fornecedores' ,'id_fornecedor');
    }

    public function telefones(): HasMany
    {
        return $this->hasMany(Telefone::class, 'id_fornecedor_fk');
    }

    public static function dtColumns(): array
    {
        $t = (new static)->getTable();
        return [
            'id' => ['db' => "{$t}.id_fornecedor", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.nome_fornecedor", 'label' => 'Fornecedor', 'order' => true, 'search' => true],
            'c2' => ['db' => "{$t}.cnpj", 'label' => 'CNPJ', 'order' => true, 'search' => true],
            'c3' => ['db' => "{$t}.cidade", 'label' => 'Cidade', 'order' => true, 'search' => true],
            'c4' => ['db' => "{$t}.uf", 'label' => 'UF', 'order' => true, 'search' => true],
            'st' => ['db' => "{$t}.status", 'label' => 'Status', 'order' => true, 'search' => false],
            'acoes' => ['computed' => true],
        ];
    }

    public static function dtFilters(): array
    {
        $t = (new static)->getTable();
        return [
            'q' => [
                'type' => 'text',
                'columns' => [
                    "{$t}.nome_fornecedor",
                    "{$t}.cnpj",
                    "{$t}.cidade",
                    "{$t}.uf",
                    "{$t}.email",
                ],
            ],
            'status' => [
                'type' => 'select',
                'column' => "{$t}.status",
                'cast' => 'int',
                'operator' => '=',
                'nullable' => true,
            ],
        ];
    }

    use HasFactory;
}
