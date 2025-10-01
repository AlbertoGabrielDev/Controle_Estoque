<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class TaxRule extends Model
{
    protected $table = 'tax_rules';
    protected $fillable = [
        'tax_id','segment_id','categoria_produto_id','ncm_padrao','uf_origem','uf_destino',
        'canal','tipo_operacao','vigencia_inicio','vigencia_fim','aliquota_percent',
        'base_formula','expression','prioridade','cumulativo'
    ];

    protected $casts = [
        'vigencia_inicio' => 'date',
        'vigencia_fim'    => 'date',
        'aliquota_percent'=> 'decimal:4',
        'cumulativo'      => 'boolean',
    ];

    public function scopeVigentes(Builder $q, ?Carbon $data = null): Builder
    {
        $data = $data ?: now();
        return $q
            ->where(function($s) use ($data) {
                $s->whereNull('vigencia_inicio')->orWhere('vigencia_inicio', '<=', $data->toDateString());
            })
            ->where(function($s) use ($data) {
                $s->whereNull('vigencia_fim')->orWhere('vigencia_fim', '>=', $data->toDateString());
            });
    }
}
