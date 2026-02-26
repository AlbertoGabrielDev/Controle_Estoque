<?php

namespace Modules\Purchases\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequisition extends Model
{
    use HasFactory;

    protected $table = 'purchase_requisitions';

    protected $fillable = [
        'numero',
        'status',
        'solicitado_por',
        'observacoes',
        'data_requisicao',
    ];

    protected $casts = [
        'data_requisicao' => 'date',
    ];

    /**
     * Get the requester user associated with this requisition.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitado_por');
    }

    /**
     * Get the items for this requisition.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionItem::class, 'requisition_id');
    }

    /**
     * Get the quotations created from this requisition.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function quotations(): HasMany
    {
        return $this->hasMany(PurchaseQuotation::class, 'requisition_id');
    }
}
