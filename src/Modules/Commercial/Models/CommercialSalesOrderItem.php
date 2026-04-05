<?php

namespace Modules\Commercial\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommercialSalesOrderItem extends Model
{
    use HasFactory;

    protected $table = 'commercial_sales_order_items';

    protected $fillable = [
        'order_id',
        'item_id',
        'descricao_snapshot',
        'unidade_medida_id',
        'quantidade',
        'quantidade_faturada',
        'preco_unit',
        'desconto_percent',
        'desconto_valor',
        'imposto_id',
        'aliquota_snapshot',
        'total_linha',
    ];

    protected $casts = [
        'quantidade' => 'decimal:3',
        'quantidade_faturada' => 'decimal:3',
        'preco_unit' => 'decimal:2',
        'desconto_percent' => 'decimal:2',
        'desconto_valor' => 'decimal:2',
        'aliquota_snapshot' => 'decimal:2',
        'total_linha' => 'decimal:2',
    ];

    /**
     * Get the sales order this item belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(CommercialSalesOrder::class, 'order_id');
    }

    /**
     * Get the item reference.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Item::class, 'item_id');
    }

    /**
     * Get the invoice items created from this order item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoiceItems(): HasMany
    {
        return $this->hasMany(CommercialSalesInvoiceItem::class, 'order_item_id');
    }

    /**
     * Get the return items related to this order item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function returnItems(): HasMany
    {
        return $this->hasMany(CommercialSalesReturnItem::class, 'order_item_id');
    }
}
