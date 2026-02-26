<?php

namespace Modules\Purchases\Models;

use App\Models\Fornecedor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseQuotationSupplier extends Model
{
    use HasFactory;

    protected $table = 'purchase_quotation_suppliers';

    protected $fillable = [
        'quotation_id',
        'supplier_id',
        'status',
        'prazo_entrega_dias',
        'condicao_pagamento',
        'observacoes',
    ];

    protected $casts = [
        'prazo_entrega_dias' => 'integer',
    ];

    /**
     * Get the quotation this supplier belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(PurchaseQuotation::class, 'quotation_id');
    }

    /**
     * Get the supplier associated with this quotation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class, 'supplier_id', 'id_fornecedor');
    }

    /**
     * Get the quoted items for this supplier.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseQuotationSupplierItem::class, 'quotation_supplier_id');
    }
}
