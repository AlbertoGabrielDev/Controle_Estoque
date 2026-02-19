<?php

namespace App\Models;

use App\Traits\HasDatatableConfig;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Produto extends Model implements Transformable
{
    use TransformableTrait;
    use HasFactory;
    use HasStatus;
    use HasDatatableConfig;
    protected $table = 'produtos';
    protected $primaryKey = 'id_produto';

    protected $fillable = [
        'cod_produto',
        'nome_produto',
        'descricao',
        'unidade_medida',
        'inf_nutriente',
        'id_users_fk',
        'qrcode',
        'status'
    ];

    protected $casts = ['inf_nutriente' => 'array'];
    public function fornecedores(): BelongsToMany
    {
        return $this->belongsToMany(Fornecedor::class, 'estoques', 'id_produto_fk', 'id_fornecedor_fk')
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

    public function categorias(): BelongsToMany
    {
        return $this->belongsToMany(Categoria::class, 'categoria_produtos', 'id_produto_fk', 'id_categoria_fk');
    }

    public function marcas(): BelongsToMany
    {
        return $this->belongsToMany(Marca::class, 'marca_produtos', 'id_produto_fk', 'id_marca_fk')->as('marca_produto');
    }

    public function vendas()
    {
        return $this->hasMany(Venda::class, 'id_produto_fk', 'id_produto');
    }

    public function taxRuleAlvos(): HasMany
    {
        return $this->hasMany(TaxRuleAlvo::class, 'id_produto_fk', 'id_produto');
    }

    /** Regras aplicáveis a este produto (via pivot condicional) */
    public function taxRules(): BelongsToMany
    {
        return $this->belongsToMany(
            TaxRule::class,
            'tax_rule_alvos',
            'id_produto_fk',
            'tax_rule_id',
            'id_produto',
            'id'
        )
            ->withTimestamps();
    }

    public static function dtColumns(): array
    {
        $t = (new static)->getTable();

        return [
            'id' => ['db' => "{$t}.id_produto", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.cod_produto", 'label' => 'Código', 'order' => true, 'search' => true],
            'c2' => ['db' => "{$t}.nome_produto", 'label' => 'Nome', 'order' => true, 'search' => true],
            'c3' => ['db' => "{$t}.descricao", 'label' => 'Descrição', 'order' => true, 'search' => true],
            'c4' => ['db' => "{$t}.unidade_medida", 'label' => 'Unidade', 'order' => true, 'search' => true],
            'c5' => ['db' => "{$t}.inf_nutriente", 'label' => 'Nutrição', 'order' => false, 'search' => false],
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
                    "{$t}.cod_produto",
                    "{$t}.nome_produto",
                    "{$t}.descricao",
                    "{$t}.unidade_medida",
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
}
