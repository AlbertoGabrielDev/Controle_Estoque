<?php

namespace Modules\Purchases\Models;

use App\Traits\HasDatatableConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseReturn extends Model
{
    use HasFactory;
    use HasDatatableConfig;

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

    /**
     * Get DataTables columns configuration for purchase returns.
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
                'db' => 'purchase_orders.numero',
                'label' => 'Pedido',
                'order' => true,
                'search' => true,
                'join' => ['purchase_orders', 'purchase_orders.id', '=', "{$t}.order_id", 'left'],
            ],
            'c4' => [
                'db' => 'purchase_receipts.numero',
                'label' => 'Recebimento',
                'order' => true,
                'search' => true,
                'join' => ['purchase_receipts', 'purchase_receipts.id', '=', "{$t}.receipt_id", 'left'],
            ],
            'c5' => ['db' => "{$t}.data_devolucao", 'label' => 'Data', 'order' => true, 'search' => false],
            'acoes' => ['computed' => true],
        ];
    }

    /**
     * Get DataTables filters configuration for purchase returns.
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
            'data_devolucao' => [
                'type' => 'daterange',
                'column' => "{$t}.data_devolucao",
                'start_param' => 'data_inicio',
                'end_param' => 'data_fim',
            ],
        ];
    }
}
