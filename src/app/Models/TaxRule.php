<?php

namespace App\Models;

use App\Enums\UF;
use App\Enums\Scope;
use App\Enums\TaxMethod;
use App\Traits\HasDatatableConfig;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class TaxRule extends Model
{
    use HasStatus;
    use HasDatatableConfig;
    protected $table = 'tax_rules';
    protected $fillable = [
        'tax_id',
        'segment_id',
        'categoria_produto_id',
        'ncm_padrao',
        'uf_origem',
        'uf_destino',
        'canal',
        'tipo_operacao',
        'vigencia_inicio',
        'vigencia_fim',
        'aliquota_percent',
        'valor_fixo',
        'base_formula',
        'expression',
        'prioridade',
        'cumulativo',
        'escopo',
        'metodo',
    ];

    protected $casts = [
        'vigencia_inicio' => 'date',
        'vigencia_fim' => 'date',
        'aliquota_percent' => 'decimal:4',
        'cumulativo' => 'boolean',
        'uf_origem' => UF::class,
        'escopo' => Scope::class,
        'metodo' => TaxMethod::class,
    ];

    public function scopeVigentes(
        Builder $q,
        \DateTimeInterface|string|null $data = null
    ): Builder {
        $d = $data ? Carbon::parse($data) : now();

        return $q
            ->where(function ($w) use ($d) {
                $w->whereNull('vigencia_inicio')->orWhere('vigencia_inicio', '<=', $d);
            })
            ->where(function ($w) use ($d) {
                $w->whereNull('vigencia_fim')->orWhere('vigencia_fim', '>=', $d);
            });
    }

    public static function dtColumns(): array
    {
        $t = (new static)->getTable(); // 'tax_rules'

        return [

            'id' => ['db' => "{$t}.id", 'label' => '#', 'order' => true, 'search' => false],

            'c1' => [
                'db' => "COALESCE(taxes.nome,'')",
                'label' => 'Nome',
                'order' => true,
                'search' => true,
                'join' => ['taxes', 'taxes.id', "=", "{$t}.tax_id", 'left'],
            ],
            'c2' => [
                'db' => "COALESCE(taxes.codigo,'')",
                'label' => 'Código',
                'order' => true,
                'search' => true,
                'join' => ['taxes', 'taxes.id', "=", "{$t}.tax_id", 'left'],
            ],
            'seg' => [
                'db' => 'customer_segments.nome',
                'select' => "COALESCE(customer_segments.nome,'')",
                'label' => 'Segmento',
                'order' => true,
                'search' => true,
                'join' => ['customer_segments', 'customer_segments.id', '=', 'tax_rules.segment_id', 'left'],
            ],

            'ufo' => ['db' => "{$t}.uf_origem", 'label' => 'UF Origem', 'order' => true, 'search' => true],
            'ufd' => ['db' => "{$t}.uf_destino", 'label' => 'UF Destino', 'order' => true, 'search' => true],
            'can' => ['db' => "{$t}.canal", 'label' => 'Canal', 'order' => true, 'search' => true],
            'op' => ['db' => "{$t}.tipo_operacao", 'label' => 'Operação', 'order' => true, 'search' => true],

            'aliq' => ['db' => "{$t}.aliquota_percent", 'label' => '% Alíquota', 'order' => true, 'search' => false],
            'prio' => ['db' => "{$t}.prioridade", 'label' => 'Prioridade', 'order' => true, 'search' => false],
            'cum' => ['db' => "{$t}.cumulativo", 'label' => 'Cumul.', 'order' => true, 'search' => false],

            'vi' => ['db' => "{$t}.vigencia_inicio", 'label' => 'Início', 'order' => true, 'search' => false],
            'vf' => ['db' => "{$t}.vigencia_fim", 'label' => 'Fim', 'order' => true, 'search' => false],
            'acoes' => ['computed' => true],
        ];
    }

    public static function dtFilters(): array
    {
        $t = (new static)->getTable();

        return [

        ];
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }
}
