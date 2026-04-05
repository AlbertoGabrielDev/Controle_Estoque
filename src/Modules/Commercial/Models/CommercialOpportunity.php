<?php

namespace Modules\Commercial\Models;

use App\Traits\HasDatatableConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Customers\Models\Cliente;

class CommercialOpportunity extends Model
{
    use HasFactory;
    use HasDatatableConfig;

    protected $table = 'commercial_opportunities';

    protected $fillable = [
        'codigo',
        'cliente_id',
        'nome',
        'descricao',
        'origem',
        'responsavel_id',
        'status',
        'valor_estimado',
        'data_prevista_fechamento',
        'motivo_perda',
        'observacoes',
    ];

    protected $casts = [
        'valor_estimado' => 'decimal:2',
        'data_prevista_fechamento' => 'date',
    ];

    /**
     * Get the customer associated with this opportunity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_cliente');
    }

    /**
     * Get the responsible user for this opportunity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'responsavel_id');
    }

    /**
     * Get the proposals created from this opportunity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proposals(): HasMany
    {
        return $this->hasMany(CommercialProposal::class, 'opportunity_id');
    }

    /**
     * Get the sales orders created from this opportunity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salesOrders(): HasMany
    {
        return $this->hasMany(CommercialSalesOrder::class, 'opportunity_id');
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
            'c1' => ['db' => "{$t}.codigo", 'label' => 'Codigo', 'order' => true, 'search' => true],
            'c2' => ['db' => "{$t}.nome", 'label' => 'Nome', 'order' => true, 'search' => true],
            'c3' => ['db' => "{$t}.status", 'label' => 'Status', 'order' => true, 'search' => true],
            'c4' => [
                'db' => "COALESCE(cl.nome_fantasia, cl.razao_social, cl.nome, '')",
                'label' => 'Cliente',
                'order' => false,
                'search' => true,
                'join' => ['clientes as cl', 'cl.id_cliente', '=', "{$t}.cliente_id", 'left'],
            ],
            'c5' => ['db' => "{$t}.valor_estimado", 'label' => 'Valor Estimado', 'order' => true, 'search' => false],
            'c6' => ['db' => "{$t}.data_prevista_fechamento", 'label' => 'Previsao', 'order' => true, 'search' => false],
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
                'columns' => ["{$t}.codigo", "{$t}.nome", 'cl.nome_fantasia', 'cl.razao_social', 'cl.nome'],
            ],
            'status' => [
                'type' => 'select',
                'column' => "{$t}.status",
                'operator' => '=',
                'nullable' => true,
            ],
            'cliente_id' => [
                'type' => 'select',
                'column' => "{$t}.cliente_id",
                'cast' => 'int',
                'operator' => '=',
                'nullable' => true,
            ],
            'responsavel_id' => [
                'type' => 'select',
                'column' => "{$t}.responsavel_id",
                'cast' => 'int',
                'operator' => '=',
                'nullable' => true,
            ],
            'data_prevista' => [
                'type' => 'daterange',
                'column' => "{$t}.data_prevista_fechamento",
                'start_param' => 'data_inicio',
                'end_param' => 'data_fim',
            ],
        ];
    }
}
