<?php

namespace Modules\Purchases\Models;

use App\Traits\HasDatatableConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Purchases\Models\PurchaseOrder;

class PurchaseQuotation extends Model
{
    use HasFactory;
    use HasDatatableConfig;

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

    /**
     * Get DataTables columns configuration for purchase quotations.
     *
     * @return array<string, array{
     *     db?: string,
     *     label?: string,
     *     order?: bool,
     *     search?: bool,
     *     join?: array{0:string,1:string,2:string,3:string,4?:'left'|'inner'|'right'},
     *     computed?: bool
     * }>
     */
    public static function dtColumns(): array
    {
        $t = (new static())->getTable();

        return [
            'id' => ['db' => "{$t}.id", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.numero", 'label' => 'Numero', 'order' => true, 'search' => true],
            'c2' => ['db' => "{$t}.status", 'label' => 'Status', 'order' => true, 'search' => true],
            'c3' => [
                'db' => 'purchase_requisitions.numero',
                'label' => 'Requisicao',
                'order' => true,
                'search' => true,
                'join' => ['purchase_requisitions', 'purchase_requisitions.id', '=', "{$t}.requisition_id", 'left'],
            ],
            'c4' => ['db' => "{$t}.data_limite", 'label' => 'Data Limite', 'order' => true, 'search' => false],
            'acoes' => ['computed' => true],
        ];
    }

    /**
     * Get DataTables filters configuration for purchase quotations.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function dtFilters(): array
    {
        $t = (new static())->getTable();

        return [
            'q' => [
                'type' => 'text',
                'columns' => ["{$t}.numero"],
            ],
            'status' => [
                'type' => 'select',
                'column' => "{$t}.status",
                'operator' => '=',
                'nullable' => true,
            ],
            'data_limite' => [
                'type' => 'daterange',
                'column' => "{$t}.data_limite",
                'start_param' => 'data_inicio',
                'end_param' => 'data_fim',
            ],
        ];
    }
}
