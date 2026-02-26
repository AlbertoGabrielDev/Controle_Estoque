<?php

namespace Modules\Purchases\Models;

use App\Models\User;
use App\Traits\HasDatatableConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequisition extends Model
{
    use HasFactory;
    use HasDatatableConfig;

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

    /**
     * Get DataTables columns configuration for purchase requisitions.
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
            'c3' => ['db' => "{$t}.data_requisicao", 'label' => 'Data', 'order' => true, 'search' => false],
            'c4' => [
                'db' => "(select count(*) from purchase_requisition_items pri where pri.requisition_id = {$t}.id)",
                'label' => 'Itens',
                'order' => false,
                'search' => false,
            ],
            'acoes' => ['computed' => true],
        ];
    }

    /**
     * Get DataTables filters configuration for purchase requisitions.
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
            'data_requisicao' => [
                'type' => 'daterange',
                'column' => "{$t}.data_requisicao",
                'start_param' => 'data_inicio',
                'end_param' => 'data_fim',
            ],
        ];
    }
}
