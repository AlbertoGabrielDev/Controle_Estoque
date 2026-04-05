<?php

namespace Modules\Commercial\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommercialSalesReturnItem extends Model
{
    use HasFactory;

    protected $table = 'commercial_sales_return_items';

    protected $fillable = [
        'return_id',
        'invoice_item_id',
        'order_item_id',
        'item_id',
        'quantidade_devolvida',
        'observacoes',
    ];

    protected $casts = [
        'quantidade_devolvida' => 'decimal:3',
    ];

    /**
     * Get the return this item belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesReturn(): BelongsTo
    {
        return $this->belongsTo(CommercialSalesReturn::class, 'return_id');
    }

    /**
     * Get the invoice item this return item references.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoiceItem(): BelongsTo
    {
        return $this->belongsTo(CommercialSalesInvoiceItem::class, 'invoice_item_id');
    }

    /**
     * Get the order item this return item references.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(CommercialSalesOrderItem::class, 'order_item_id');
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
