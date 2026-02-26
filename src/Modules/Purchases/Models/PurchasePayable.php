<?php

namespace Modules\Purchases\Models;

use App\Models\Fornecedor;
use App\Traits\HasDatatableConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchasePayable extends Model
{
    use HasFactory;
    use HasDatatableConfig;

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

    /**
     * Get DataTables columns configuration for purchase payables.
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
            'c1' => ['db' => "{$t}.numero_documento", 'label' => 'Documento', 'order' => true, 'search' => true],
            'c2' => ['db' => "{$t}.status", 'label' => 'Status', 'order' => true, 'search' => true],
            'c3' => [
                'db' => "COALESCE(fornecedores.razao_social, fornecedores.nome_fornecedor)",
                'label' => 'Fornecedor',
                'order' => true,
                'search' => true,
                'join' => ['fornecedores', 'fornecedores.id_fornecedor', '=', "{$t}.supplier_id", 'left'],
            ],
            'c4' => ['db' => "{$t}.data_vencimento", 'label' => 'Vencimento', 'order' => true, 'search' => false],
            'c5' => ['db' => "{$t}.valor_total", 'label' => 'Valor', 'order' => true, 'search' => false],
            'acoes' => ['computed' => true],
        ];
    }

    /**
     * Get DataTables filters configuration for purchase payables.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function dtFilters(): array
    {
        $t = (new static())->getTable();

        return [
            'q' => [
                'type' => 'text',
                'columns' => ["{$t}.numero_documento"],
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
            'data_vencimento' => [
                'type' => 'daterange',
                'column' => "{$t}.data_vencimento",
                'start_param' => 'data_inicio',
                'end_param' => 'data_fim',
            ],
        ];
    }
}
