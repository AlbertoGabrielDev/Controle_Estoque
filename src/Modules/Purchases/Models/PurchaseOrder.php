<?php

namespace Modules\Purchases\Models;

use App\Models\Fornecedor;
use App\Traits\HasDatatableConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;
    use HasDatatableConfig;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'numero',
        'status',
        'supplier_id',
        'quotation_id',
        'data_emissao',
        'data_prevista',
        'observacoes',
        'total',
    ];

    protected $casts = [
        'data_emissao' => 'date',
        'data_prevista' => 'date',
        'total' => 'decimal:2',
    ];

    /**
     * Get the supplier associated with this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class, 'supplier_id', 'id_fornecedor');
    }

    /**
     * Get the quotation that originated this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(PurchaseQuotation::class, 'quotation_id');
    }

    /**
     * Get the items for this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class, 'order_id');
    }

    /**
     * Get the receipts linked to this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receipts(): HasMany
    {
        return $this->hasMany(PurchaseReceipt::class, 'order_id');
    }

    /**
     * Get the returns linked to this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function returns(): HasMany
    {
        return $this->hasMany(PurchaseReturn::class, 'order_id');
    }

    /**
     * Get the payables generated for this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payables(): HasMany
    {
        return $this->hasMany(PurchasePayable::class, 'order_id');
    }

    /**
     * Get DataTables columns configuration for purchase orders.
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
                'db' => "COALESCE(fornecedores.razao_social, fornecedores.nome_fornecedor)",
                'label' => 'Fornecedor',
                'order' => true,
                'search' => true,
                'join' => ['fornecedores', 'fornecedores.id_fornecedor', '=', "{$t}.supplier_id", 'left'],
            ],
            'c4' => ['db' => "{$t}.data_emissao", 'label' => 'Data', 'order' => true, 'search' => false],
            'c5' => ['db' => "{$t}.total", 'label' => 'Total', 'order' => true, 'search' => false],
            'acoes' => ['computed' => true],
        ];
    }

    /**
     * Get DataTables filters configuration for purchase orders.
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
            'data_emissao' => [
                'type' => 'daterange',
                'column' => "{$t}.data_emissao",
                'start_param' => 'data_inicio',
                'end_param' => 'data_fim',
            ],
        ];
    }
}
