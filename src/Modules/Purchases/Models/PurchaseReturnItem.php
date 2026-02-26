<?php

namespace Modules\Purchases\Models;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseReturnItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_return_items';

    protected $fillable = [
        'return_id',
        'receipt_item_id',
        'order_item_id',
        'item_id',
        'quantidade_devolvida',
        'observacoes',
    ];

    protected $casts = [
        'quantidade_devolvida' => 'decimal:3',
    ];

    /**
     * Get the return associated with this item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchaseReturn(): BelongsTo
    {
        return $this->belongsTo(PurchaseReturn::class, 'return_id');
    }

    /**
     * Get the receipt item linked to this return entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiptItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseReceiptItem::class, 'receipt_item_id');
    }

    /**
     * Get the order item linked to this return entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'order_item_id');
    }

    /**
     * Get the item referenced by this return entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
