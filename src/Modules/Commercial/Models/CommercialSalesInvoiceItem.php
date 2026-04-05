<?php

namespace Modules\Commercial\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommercialSalesInvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'commercial_sales_invoice_items';

    protected $fillable = [
        'invoice_id',
        'order_item_id',
        'item_id',
        'descricao_snapshot',
        'quantidade_faturada',
        'preco_unit',
        'desconto_percent',
        'desconto_valor',
        'imposto_id',
        'aliquota_snapshot',
        'total_linha',
    ];

    protected $casts = [
        'quantidade_faturada' => 'decimal:3',
        'preco_unit' => 'decimal:2',
        'desconto_percent' => 'decimal:2',
        'desconto_valor' => 'decimal:2',
        'aliquota_snapshot' => 'decimal:2',
        'total_linha' => 'decimal:2',
    ];

    /**
     * Get the invoice this item belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(CommercialSalesInvoice::class, 'invoice_id');
    }

    /**
     * Get the originating sales order item.
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

    /**
     * Get the return items related to this invoice item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function returnItems(): HasMany
    {
        return $this->hasMany(CommercialSalesReturnItem::class, 'invoice_item_id');
    }
}
