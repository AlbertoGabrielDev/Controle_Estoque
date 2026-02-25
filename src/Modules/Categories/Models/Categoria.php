<?php

namespace Modules\Categories\Models;

use App\Models\TaxRule;
use App\Models\TaxRuleAlvo;
use App\Traits\HasDatatableConfig;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Products\Models\Produto;

class Categoria extends Model
{
    use HasFactory;
    use HasStatus;
    use HasDatatableConfig;

    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';
    protected $statusColumn = 'ativo';

    protected $fillable = [
        'codigo',
        'nome_categoria',
        'tipo',
        'categoria_pai_id',
        'id_users_fk',
        'imagem',
        'status',
        'ativo',
    ];

    protected $casts = [
        'status' => 'integer',
        'ativo' => 'boolean',
    ];

    public function produtos(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'categoria_produtos', 'id_categoria_fk', 'id_produto_fk');
    }

    public function categoriaPai()
    {
        return $this->belongsTo(self::class, 'categoria_pai_id', 'id_categoria');
    }

    public function filhas()
    {
        return $this->hasMany(self::class, 'categoria_pai_id', 'id_categoria');
    }

    public function taxRuleAlvos(): HasMany
    {
        return $this->hasMany(TaxRuleAlvo::class, 'id_categoria_fk', 'id_categoria');
    }

    public function taxRules(): BelongsToMany
    {
        return $this->belongsToMany(
            TaxRule::class,
            'tax_rule_alvos',
            'id_categoria_fk',
            'tax_rule_id',
            'id_categoria',
            'id'
        )->withTimestamps();
    }

    protected static function booted(): void
    {
        static::saving(function (self $model) {
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
            'id' => ['db' => "{$t}.id_categoria", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.codigo", 'label' => 'Codigo', 'order' => true, 'search' => true],
            'c2' => ['db' => "{$t}.nome_categoria", 'label' => 'Categoria', 'order' => true, 'search' => true],
            'c3' => ['db' => "{$t}.tipo", 'label' => 'Tipo', 'order' => true, 'search' => true],
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
                    "{$t}.nome_categoria",
                ],
            ],
            'status' => [
                'type' => 'select',
                'column' => "{$t}.status",
                'cast' => 'int',
                'operator' => '=',
                'nullable' => true,
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
}
