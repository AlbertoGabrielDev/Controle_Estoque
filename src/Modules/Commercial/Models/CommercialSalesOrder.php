<?php

namespace Modules\Commercial\Models;

use App\Traits\HasDatatableConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Customers\Models\Cliente;

class CommercialSalesOrder extends Model
{
    use HasFactory;
    use HasDatatableConfig;

    protected $table = 'commercial_sales_orders';

    protected $fillable = [
        'numero',
        'proposal_id',
        'opportunity_id',
        'cliente_id',
        'status',
        'data_pedido',
        'observacoes',
        'subtotal',
        'desconto_total',
        'total_impostos',
        'total',
    ];

    protected $casts = [
        'data_pedido' => 'date',
        'subtotal' => 'decimal:2',
        'desconto_total' => 'decimal:2',
        'total_impostos' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the proposal this order was created from.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(CommercialProposal::class, 'proposal_id');
    }

    /**
     * Get the opportunity related to this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(CommercialOpportunity::class, 'opportunity_id');
    }

    /**
     * Get the customer for this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_cliente');
    }

    /**
     * Get the items for this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(CommercialSalesOrderItem::class, 'order_id');
    }

    /**
     * Get the invoices for this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(CommercialSalesInvoice::class, 'order_id');
    }

    /**
     * Get the returns related to this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function returns(): HasMany
    {
        return $this->hasMany(CommercialSalesReturn::class, 'order_id');
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
            'c1' => ['db' => "{$t}.numero", 'label' => 'Numero', 'order' => true, 'search' => true],
            'c2' => ['db' => "{$t}.status", 'label' => 'Status', 'order' => true, 'search' => true],
            'c3' => [
                'db' => "COALESCE(cl.nome_fantasia, cl.razao_social, cl.nome, '')",
                'label' => 'Cliente',
                'order' => false,
                'search' => true,
                'join' => ['clientes as cl', 'cl.id_cliente', '=', "{$t}.cliente_id", 'left'],
            ],
            'c4' => ['db' => "{$t}.data_pedido", 'label' => 'Data Pedido', 'order' => true, 'search' => false],
            'c5' => ['db' => "{$t}.total", 'label' => 'Total', 'order' => true, 'search' => false],
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
                'columns' => ["{$t}.numero", 'cl.nome_fantasia', 'cl.razao_social', 'cl.nome'],
            ],
            'status' => [
                'type' => 'select',
                'column' => "{$t}.status",
                'operator' => '=',
                'nullable' => true,
            ],
            'data_pedido' => [
                'type' => 'daterange',
                'column' => "{$t}.data_pedido",
                'start_param' => 'data_inicio',
                'end_param' => 'data_fim',
            ],
        ];
    }
}
