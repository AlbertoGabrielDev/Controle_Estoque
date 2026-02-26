<?php

namespace Modules\Purchases\Models;

use App\Models\Item;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseQuotationSupplierItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_quotation_supplier_items';

    protected $fillable = [
        'quotation_supplier_id',
        'requisition_item_id',
        'item_id',
        'quantidade',
        'preco_unit',
        'imposto_id',
        'aliquota_snapshot',
        'selecionado',
    ];

    protected $casts = [
        'quantidade' => 'decimal:3',
        'preco_unit' => 'decimal:2',
        'aliquota_snapshot' => 'decimal:2',
        'selecionado' => 'boolean',
    ];

    /**
     * Get the quotation supplier for this item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quotationSupplier(): BelongsTo
    {
        return $this->belongsTo(PurchaseQuotationSupplier::class, 'quotation_supplier_id');
    }

    /**
     * Get the requisition item referenced by this quotation entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requisitionItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisitionItem::class, 'requisition_item_id');
    }

    /**
     * Get the master item referenced by this quotation entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Get the tax applied to this quotation entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function imposto(): BelongsTo
    {
        return $this->belongsTo(Tax::class, 'imposto_id');
    }
}
