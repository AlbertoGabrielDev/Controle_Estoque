<?php

namespace Modules\Purchases\Models;

use App\Models\Fornecedor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseReceipt extends Model
{
    use HasFactory;

    protected $table = 'purchase_receipts';

    protected $fillable = [
        'numero',
        'status',
        'order_id',
        'supplier_id',
        'data_recebimento',
        'observacoes',
    ];

    protected $casts = [
        'data_recebimento' => 'date',
    ];

    /**
     * Get the order associated with this receipt.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'order_id');
    }

    /**
     * Get the supplier associated with this receipt.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class, 'supplier_id', 'id_fornecedor');
    }

    /**
     * Get the receipt items linked to this receipt.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseReceiptItem::class, 'receipt_id');
    }

    /**
     * Get the returns linked to this receipt.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function returns(): HasMany
    {
        return $this->hasMany(PurchaseReturn::class, 'receipt_id');
    }

    /**
     * Get the payables generated from this receipt.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payables(): HasMany
    {
        return $this->hasMany(PurchasePayable::class, 'receipt_id');
    }
}
