<?php

namespace Modules\Purchases\Models;

use App\Models\Item;
use App\Models\Tax;
use App\Models\UnidadeMedida;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_items';

    protected $fillable = [
        'order_id',
        'item_id',
        'descricao_snapshot',
        'unidade_medida_id',
        'quantidade_pedida',
        'quantidade_recebida',
        'preco_unit',
        'imposto_id',
        'aliquota_snapshot',
        'total_linha',
    ];

    protected $casts = [
        'quantidade_pedida' => 'decimal:3',
        'quantidade_recebida' => 'decimal:3',
        'preco_unit' => 'decimal:2',
        'aliquota_snapshot' => 'decimal:2',
        'total_linha' => 'decimal:2',
    ];

    /**
     * Get the order that owns this item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'order_id');
    }

    /**
     * Get the item referenced by this order entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Get the unit of measure used by this order entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unidadeMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadeMedida::class, 'unidade_medida_id');
    }

    /**
     * Get the tax applied to this order entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function imposto(): BelongsTo
    {
        return $this->belongsTo(Tax::class, 'imposto_id');
    }

    /**
     * Get the receipt items linked to this order entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receiptItems(): HasMany
    {
        return $this->hasMany(PurchaseReceiptItem::class, 'order_item_id');
    }

    /**
     * Get the return items linked to this order entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function returnItems(): HasMany
    {
        return $this->hasMany(PurchaseReturnItem::class, 'order_item_id');
    }
}
