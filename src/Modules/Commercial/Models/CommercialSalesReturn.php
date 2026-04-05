<?php

namespace Modules\Commercial\Models;

use App\Traits\HasDatatableConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Customers\Models\Cliente;

class CommercialSalesReturn extends Model
{
    use HasFactory;
    use HasDatatableConfig;

    protected $table = 'commercial_sales_returns';

    protected $fillable = [
        'numero',
        'invoice_id',
        'order_id',
        'cliente_id',
        'status',
        'motivo',
        'data_devolucao',
    ];

    protected $casts = [
        'data_devolucao' => 'date',
    ];

    /**
     * Get the invoice this return is based on.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(CommercialSalesInvoice::class, 'invoice_id');
    }

    /**
     * Get the sales order related to this return.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(CommercialSalesOrder::class, 'order_id');
    }

    /**
     * Get the customer for this return.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_cliente');
    }

    /**
     * Get the items for this return.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(CommercialSalesReturnItem::class, 'return_id');
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
            'c4' => ['db' => "{$t}.data_devolucao", 'label' => 'Data Devolucao', 'order' => true, 'search' => false],
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
            'data_devolucao' => [
                'type' => 'daterange',
                'column' => "{$t}.data_devolucao",
                'start_param' => 'data_inicio',
                'end_param' => 'data_fim',
            ],
        ];
    }
}
