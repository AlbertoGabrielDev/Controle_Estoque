<?php

namespace Modules\Purchases\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseReturn extends Model
{
    use HasFactory;

    protected $table = 'purchase_returns';

    protected $fillable = [
        'numero',
        'status',
        'receipt_id',
        'order_id',
        'motivo',
        'data_devolucao',
    ];

    protected $casts = [
        'data_devolucao' => 'date',
    ];

    /**
     * Get the receipt associated with this return.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receipt(): BelongsTo
    {
        return $this->belongsTo(PurchaseReceipt::class, 'receipt_id');
    }

    /**
     * Get the order associated with this return.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'order_id');
    }

    /**
     * Get the return items linked to this return.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseReturnItem::class, 'return_id');
    }
}
