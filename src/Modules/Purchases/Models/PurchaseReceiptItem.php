<?php

namespace Modules\Purchases\Models;

use App\Models\Item;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseReceiptItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_receipt_items';

    protected $fillable = [
        'receipt_id',
        'order_item_id',
        'item_id',
        'quantidade_recebida',
        'preco_unit_recebido',
        'imposto_id',
        'aliquota_snapshot',
        'divergencia_flag',
        'motivo_divergencia',
    ];

    protected $casts = [
        'quantidade_recebida' => 'decimal:3',
        'preco_unit_recebido' => 'decimal:2',
        'aliquota_snapshot' => 'decimal:2',
        'divergencia_flag' => 'boolean',
    ];

    /**
     * Get the receipt that owns this item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receipt(): BelongsTo
    {
        return $this->belongsTo(PurchaseReceipt::class, 'receipt_id');
    }

    /**
     * Get the order item linked to this receipt entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'order_item_id');
    }

    /**
     * Get the item referenced by this receipt entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Get the tax applied to this receipt entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function imposto(): BelongsTo
    {
        return $this->belongsTo(Tax::class, 'imposto_id');
    }

    /**
     * Get the return items linked to this receipt entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function returnItems(): HasMany
    {
        return $this->hasMany(PurchaseReturnItem::class, 'receipt_item_id');
    }
}
