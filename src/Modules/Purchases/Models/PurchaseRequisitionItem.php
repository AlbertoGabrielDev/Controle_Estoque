<?php

namespace Modules\Purchases\Models;

use App\Models\Item;
use App\Models\Tax;
use App\Models\UnidadeMedida;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequisitionItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_requisition_items';

    protected $fillable = [
        'requisition_id',
        'item_id',
        'descricao_snapshot',
        'unidade_medida_id',
        'quantidade',
        'preco_estimado',
        'imposto_id',
        'observacoes',
    ];

    protected $casts = [
        'quantidade' => 'decimal:3',
        'preco_estimado' => 'decimal:2',
    ];

    /**
     * Get the requisition for this item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class, 'requisition_id');
    }

    /**
     * Get the item referenced by this requisition entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Get the unit of measure used by this requisition entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unidadeMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadeMedida::class, 'unidade_medida_id');
    }

    /**
     * Get the tax applied to this requisition entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function imposto(): BelongsTo
    {
        return $this->belongsTo(Tax::class, 'imposto_id');
    }
}
