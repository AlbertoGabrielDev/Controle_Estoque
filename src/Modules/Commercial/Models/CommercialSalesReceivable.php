<?php

namespace Modules\Commercial\Models;

use App\Traits\HasDatatableConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Customers\Models\Cliente;

class CommercialSalesReceivable extends Model
{
    use HasFactory;
    use HasDatatableConfig;

    protected $table = 'commercial_sales_receivables';

    protected $fillable = [
        'numero_documento',
        'invoice_id',
        'order_id',
        'cliente_id',
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
     * Get the invoice that generated this receivable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(CommercialSalesInvoice::class, 'invoice_id');
    }

    /**
     * Get the sales order related to this receivable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(CommercialSalesOrder::class, 'order_id');
    }

    /**
     * Get the customer for this receivable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_cliente');
    }

    /**
     * Get DataTables columns configuration.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function dtColumns(): array
    {
        $t = (new static())->getTable();

        return [
            'id' => ['db' => "{$t}.id", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.numero_documento", 'label' => 'Documento', 'order' => true, 'search' => true],
            'c2' => ['db' => "{$t}.status", 'label' => 'Status', 'order' => true, 'search' => true],
            'c3' => [
                'db' => "COALESCE(cl.nome_fantasia, cl.razao_social, cl.nome, '')",
                'label' => 'Cliente',
                'order' => false,
                'search' => true,
                'join' => ['clientes as cl', 'cl.id_cliente', '=', "{$t}.cliente_id", 'left'],
            ],
            'c4' => ['db' => "{$t}.data_emissao", 'label' => 'Emissao', 'order' => true, 'search' => false],
            'c5' => ['db' => "{$t}.data_vencimento", 'label' => 'Vencimento', 'order' => true, 'search' => false],
            'c6' => ['db' => "{$t}.valor_total", 'label' => 'Valor', 'order' => true, 'search' => false],
            'acoes' => ['computed' => true],
        ];
    }

    /**
     * Get DataTables filters configuration.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function dtFilters(): array
    {
        $t = (new static())->getTable();

        return [
            'q' => [
                'type' => 'text',
                'columns' => ["{$t}.numero_documento", 'cl.nome_fantasia', 'cl.razao_social', 'cl.nome'],
            ],
            'status' => [
                'type' => 'select',
                'column' => "{$t}.status",
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
