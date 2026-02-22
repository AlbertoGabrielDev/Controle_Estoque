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
    protected $statusColumn = 'ativo';

    protected $fillable = [
        'id_fornecedor',
        'codigo',
        'razao_social',
        'nome_fornecedor',
        'cnpj',
        'nif_cif',
        'cep',
        'logradouro',
        'bairro',
        'numero_casa',
        'email',
        'cidade',
        'uf',
        'endereco',
        'prazo_entrega_dias',
        'condicao_pagamento',
        'id_users_fk',
        'id_cidade_fk',
        'status',
        'ativo',
    ];

    protected $casts = [
        'status' => 'integer',
        'ativo' => 'boolean',
        'prazo_entrega_dias' => 'integer',
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

    protected static function booted(): void
    {
        static::saving(function (Fornecedor $model) {
            if (!is_null($model->ativo)) {
                $model->status = $model->ativo ? 1 : 0;
                return;
            }
            if (!is_null($model->status)) {
                $model->ativo = (int) $model->status === 1;
            }
        });
    }

    public static function dtColumns(): array
    {
        $t = (new static)->getTable();
        return [
            'id' => ['db' => "{$t}.id_fornecedor", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.codigo", 'label' => 'Código', 'order' => true, 'search' => true],
            'c2' => ['db' => "COALESCE({$t}.razao_social, {$t}.nome_fornecedor)", 'label' => 'Fornecedor', 'order' => true, 'search' => true],
            'c3' => ['db' => "COALESCE({$t}.nif_cif, {$t}.cnpj)", 'label' => 'NIF/CIF', 'order' => true, 'search' => true],
            'c4' => ['db' => "{$t}.cidade", 'label' => 'Cidade', 'order' => true, 'search' => true],
            'c5' => ['db' => "{$t}.uf", 'label' => 'UF', 'order' => true, 'search' => true],
            'st' => ['db' => "{$t}.ativo", 'label' => 'Ativo', 'order' => true, 'search' => false],
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
                    "{$t}.codigo",
                    "{$t}.razao_social",
                    "{$t}.nome_fornecedor",
                    "{$t}.cnpj",
                    "{$t}.nif_cif",
                    "{$t}.cidade",
                    "{$t}.uf",
                    "{$t}.email",
                ],
            ],
            'ativo' => [
                'type' => 'select',
                'column' => "{$t}.ativo",
                'cast' => 'int',
                'operator' => '=',
                'nullable' => true,
            ],
        ];
    }

    use HasFactory;
}
