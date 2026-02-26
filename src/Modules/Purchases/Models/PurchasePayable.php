<?php

namespace Modules\Purchases\Models;

use App\Models\Fornecedor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchasePayable extends Model
{
    use HasFactory;

    protected $table = 'purchase_payables';

    protected $fillable = [
        'supplier_id',
        'order_id',
        'receipt_id',
        'numero_documento',
        'data_emissao',
        'data_vencimento',
        'valor_total',
        'status',
    ];

    protected $casts = [
        'data_emissao' => 'date',
        'data_vencimento' => 'date',
        'valor_total' => 'decimal:2',
    ];

    /**
     * Get the supplier associated with this payable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class, 'supplier_id', 'id_fornecedor');
    }

    /**
     * Get the order associated with this payable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'order_id');
    }

    /**
     * Get the receipt associated with this payable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receipt(): BelongsTo
    {
        return $this->belongsTo(PurchaseReceipt::class, 'receipt_id');
    }
}
