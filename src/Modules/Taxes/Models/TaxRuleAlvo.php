<?php

namespace Modules\Taxes\Models;

use App\Models\Categoria;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxRuleAlvo extends Model
{
    protected $table = 'tax_rule_alvos';

    protected $fillable = [
        'tax_rule_id',
        'id_categoria_fk',
        'id_produto_fk',
    ];

    /** Regra de imposto dona deste alvo */
    public function rule(): BelongsTo
    {
        return $this->belongsTo(TaxRule::class, 'tax_rule_id', 'id');
    }

    /** Categoria apontada (quando não for null) */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'id_categoria_fk', 'id_categoria');
    }

    /** Produto apontado (quando não for null) */
    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'id_produto_fk', 'id_produto');
    }

    /* ---------- Scopes úteis ---------- */

    public function scopeParaCategoria($q, $categoriaId)
    {
        return $q->where('id_categoria_fk', (int) $categoriaId);
    }

    public function scopeParaProduto($q, $produtoId)
    {
        return $q->where('id_produto_fk', (int) $produtoId);
    }
}
