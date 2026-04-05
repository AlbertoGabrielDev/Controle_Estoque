<?php

namespace Modules\Commercial\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommercialProposalItem extends Model
{
    use HasFactory;

    protected $table = 'commercial_proposal_items';

    protected $fillable = [
        'proposal_id',
        'item_id',
        'descricao_snapshot',
        'unidade_medida_id',
        'quantidade',
        'preco_unit',
        'desconto_percent',
        'desconto_valor',
        'imposto_id',
        'aliquota_snapshot',
        'total_linha',
    ];

    protected $casts = [
        'quantidade' => 'decimal:3',
        'preco_unit' => 'decimal:2',
        'desconto_percent' => 'decimal:2',
        'desconto_valor' => 'decimal:2',
        'aliquota_snapshot' => 'decimal:2',
        'total_linha' => 'decimal:2',
    ];

    /**
     * Get the proposal this item belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(CommercialProposal::class, 'proposal_id');
    }

    /**
     * Get the item (product/service) reference.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Item::class, 'item_id');
    }
}
