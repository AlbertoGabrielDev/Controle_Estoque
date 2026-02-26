<?php

namespace Modules\Purchases\Models;

use App\Models\Fornecedor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'numero',
        'status',
        'supplier_id',
        'quotation_id',
        'data_emissao',
        'data_prevista',
        'observacoes',
        'total',
    ];

    protected $casts = [
        'data_emissao' => 'date',
        'data_prevista' => 'date',
        'total' => 'decimal:2',
    ];

    /**
     * Get the supplier associated with this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class, 'supplier_id', 'id_fornecedor');
    }

    /**
     * Get the quotation that originated this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(PurchaseQuotation::class, 'quotation_id');
    }

    /**
     * Get the items for this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class, 'order_id');
    }

    /**
     * Get the receipts linked to this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receipts(): HasMany
    {
        return $this->hasMany(PurchaseReceipt::class, 'order_id');
    }

    /**
     * Get the returns linked to this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function returns(): HasMany
    {
        return $this->hasMany(PurchaseReturn::class, 'order_id');
    }

    /**
     * Get the payables generated for this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payables(): HasMany
    {
        return $this->hasMany(PurchasePayable::class, 'order_id');
    }
}
