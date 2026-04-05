<?php

namespace Modules\Commercial\Models;

use App\Traits\HasDatatableConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Customers\Models\Cliente;

class CommercialProposal extends Model
{
    use HasFactory;
    use HasDatatableConfig;

    protected $table = 'commercial_proposals';

    protected $fillable = [
        'numero',
        'opportunity_id',
        'cliente_id',
        'status',
        'data_emissao',
        'validade_ate',
        'observacoes',
        'subtotal',
        'desconto_total',
        'total_impostos',
        'total',
    ];

    protected $casts = [
        'data_emissao' => 'date',
        'validade_ate' => 'date',
        'subtotal' => 'decimal:2',
        'desconto_total' => 'decimal:2',
        'total_impostos' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the opportunity associated with this proposal.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(CommercialOpportunity::class, 'opportunity_id');
    }

    /**
     * Get the customer associated with this proposal.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_cliente');
    }

    /**
     * Get the items for this proposal.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(CommercialProposalItem::class, 'proposal_id');
    }

    /**
     * Get the sales orders created from this proposal.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salesOrders(): HasMany
    {
        return $this->hasMany(CommercialSalesOrder::class, 'proposal_id');
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
            'c4' => ['db' => "{$t}.data_emissao", 'label' => 'Emissao', 'order' => true, 'search' => false],
            'c5' => ['db' => "{$t}.validade_ate", 'label' => 'Validade', 'order' => true, 'search' => false],
            'c6' => ['db' => "{$t}.total", 'label' => 'Total', 'order' => true, 'search' => false],
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
            'data_emissao' => [
                'type' => 'daterange',
                'column' => "{$t}.data_emissao",
                'start_param' => 'data_inicio',
                'end_param' => 'data_fim',
            ],
        ];
    }
}
