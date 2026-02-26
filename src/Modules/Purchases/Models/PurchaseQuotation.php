<?php

namespace Modules\Purchases\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Purchases\Models\PurchaseOrder;

class PurchaseQuotation extends Model
{
    use HasFactory;

    protected $table = 'purchase_quotations';

    protected $fillable = [
        'numero',
        'status',
        'requisition_id',
        'data_limite',
        'observacoes',
    ];

    protected $casts = [
        'data_limite' => 'date',
    ];

    /**
     * Get the requisition that originated this quotation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class, 'requisition_id');
    }

    /**
     * Get the suppliers invited to this quotation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function suppliers(): HasMany
    {
        return $this->hasMany(PurchaseQuotationSupplier::class, 'quotation_id');
    }

    /**
     * Get the purchase orders generated from this quotation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'quotation_id');
    }
}
