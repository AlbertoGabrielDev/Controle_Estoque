<?php

namespace Modules\Purchases\Models;

use App\Models\Fornecedor;
use App\Traits\HasDatatableConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseReceipt extends Model
{
    use HasFactory;
    use HasDatatableConfig;

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

    /**
     * Get DataTables columns configuration for purchase receipts.
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
                'db' => "COALESCE(fornecedores.razao_social, fornecedores.nome_fornecedor)",
                'label' => 'Fornecedor',
                'order' => true,
                'search' => true,
                'join' => ['fornecedores', 'fornecedores.id_fornecedor', '=', "{$t}.supplier_id", 'left'],
            ],
            'c5' => ['db' => "{$t}.data_recebimento", 'label' => 'Data', 'order' => true, 'search' => false],
            'acoes' => ['computed' => true],
        ];
    }

    /**
     * Get DataTables filters configuration for purchase receipts.
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
            'supplier_id' => [
                'type' => 'select',
                'column' => "{$t}.supplier_id",
                'cast' => 'int',
                'operator' => '=',
                'nullable' => true,
            ],
            'data_recebimento' => [
                'type' => 'daterange',
                'column' => "{$t}.data_recebimento",
                'start_param' => 'data_inicio',
                'end_param' => 'data_fim',
            ],
        ];
    }
}
