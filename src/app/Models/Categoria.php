<?php

namespace App\Models;

use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    use HasFactory;
    use HasStatus;
    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'nome_categoria',
        'id_users_fk',
        'imagem',
        'status'
    ];

    public function produtos(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'categoria_produtos', 'id_categoria_fk', 'id_produto_fk');
    }

    public function taxRuleAlvos(): HasMany
    {
        return $this->hasMany(TaxRuleAlvo::class, 'id_categoria_fk', 'id_categoria');
    }

    /** Regras aplicáveis a esta categoria (via pivot condicional) */
    public function taxRules(): BelongsToMany
    {
        return $this->belongsToMany(
            TaxRule::class,
            'tax_rule_alvos',
            'id_categoria_fk',  // FK p/ esta tabela no pivô
            'tax_rule_id',      // FK p/ tax_rules no pivô
            'id_categoria',     // PK local
            'id'                // PK da tabela tax_rules
        )
            ->withTimestamps();
    }
}
