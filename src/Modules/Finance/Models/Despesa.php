<?php

namespace Modules\Finance\Models;

use App\Models\Fornecedor;
use App\Traits\HasDatatableConfig;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Despesa extends Model
{
    use HasStatus;
    use HasDatatableConfig;

    protected $table = 'despesas';
    protected $statusColumn = 'ativo';

    protected $fillable = [
        'data',
        'descricao',
        'valor',
        'centro_custo_id',
        'conta_contabil_id',
        'fornecedor_id',
        'documento',
        'observacoes',
        'ativo',
    ];

    protected $casts = [
        'data' => 'date',
        'valor' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    public function centroCusto(): BelongsTo
    {
        return $this->belongsTo(CentroCusto::class, 'centro_custo_id');
    }

    public function contaContabil(): BelongsTo
    {
        return $this->belongsTo(ContaContabil::class, 'conta_contabil_id');
    }

    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class, 'fornecedor_id', 'id_fornecedor');
    }

    public static function dtColumns(): array
    {
        $t = (new static)->getTable();
        return [
            'id' => ['db' => "{$t}.id", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.data", 'label' => 'Data', 'order' => true, 'search' => false],
            'c2' => ['db' => "{$t}.descricao", 'label' => 'Descrição', 'order' => true, 'search' => true],
            'c3' => ['db' => "{$t}.valor", 'label' => 'Valor', 'order' => true, 'search' => false],
            'c4' => [
                'db' => 'cc.codigo',
                'label' => 'Centro de Custo',
                'order' => false,
                'search' => true,
                'join' => ["centros_custo as cc", 'cc.id', '=', "{$t}.centro_custo_id", 'left'],
            ],
            'c5' => [
                'db' => 'ctb.codigo',
                'label' => 'Conta Contábil',
                'order' => false,
                'search' => true,
                'join' => ["contas_contabeis as ctb", 'ctb.id', '=', "{$t}.conta_contabil_id", 'left'],
            ],
            'c6' => [
                'db' => "COALESCE(f.nome_fornecedor, f.razao_social, '')",
                'label' => 'Fornecedor',
                'order' => false,
                'search' => true,
                'join' => ['fornecedores as f', 'f.id_fornecedor', '=', "{$t}.fornecedor_id", 'left'],
            ],
            'st' => ['db' => "{$t}.ativo", 'label' => 'Ativo', 'order' => true, 'search' => false],
            'acoes' => ['computed' => true],
        ];
    }

    public static function dtFilters(): array
    {
        $t = (new static)->getTable();
        return [
            'q' => [
                'type' => 'text',
                'columns' => [
                    "{$t}.descricao",
                    "{$t}.documento",
                    'cc.codigo',
                    'cc.nome',
                    'ctb.codigo',
                    'ctb.nome',
                    "COALESCE(f.nome_fornecedor, f.razao_social, '')",
                ],
            ],
            'centro_custo_id' => [
                'type' => 'select',
                'column' => "{$t}.centro_custo_id",
                'cast' => 'int',
                'operator' => '=',
                'nullable' => true,
            ],
            'conta_contabil_id' => [
                'type' => 'select',
                'column' => "{$t}.conta_contabil_id",
                'cast' => 'int',
                'operator' => '=',
                'nullable' => true,
            ],
            'fornecedor_id' => [
                'type' => 'select',
                'column' => "{$t}.fornecedor_id",
                'cast' => 'int',
                'operator' => '=',
                'nullable' => true,
            ],
            'ativo' => [
                'type' => 'select',
                'column' => "{$t}.ativo",
                'cast' => 'int',
                'operator' => '=',
                'nullable' => true,
            ],
            'data' => [
                'type' => 'daterange',
                'column' => "{$t}.data",
                'start_param' => 'data_inicio',
                'end_param' => 'data_fim',
            ],
        ];
    }
}
