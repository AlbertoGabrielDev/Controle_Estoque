<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Trait para construir query + columnsMap para DataTables a partir da Model.
 *
 * A Model deve implementar:
 *  - public static function dtColumns(): array
 *  - public static function dtFilters(): array
 *
 * Formato dtColumns():
 * [
 *   'id'  => ['db' => 'clientes.id_cliente', 'label'=>'#', 'order'=>true, 'search'=>false],
 *   'c1'  => ['db' => 'clientes.nome',       'label'=>'Nome', 'order'=>true, 'search'=>true],
 *   'seg' => [
 *     'db'   => 'customer_segments.nome',
 *     'join' => ['customer_segments','customer_segments.id','=','clientes.segment_id','left'],
 *     'order'=>false,'search'=>true
 *   ],
 *   'st'  => ['db'=>'clientes.status','order'=>true,'search'=>false],
 *   'acoes'=>['computed'=>true], // coluna calculada
 * ]
 *
 * Formato dtFilters():
 * [
 *   'q' => ['type'=>'text','columns'=>['clientes.nome','clientes.email', ...]],
 *   'uf'=> ['type'=>'select','column'=>'clientes.uf','operator'=>'=','transform'=>'upper','nullable'=>true],
 *   'status'=>['type'=>'select','column'=>'clientes.status','cast'=>'int','operator'=>'=','nullable'=>true],
 *   'created_at'=>['type'=>'date','column'=>'clientes.created_at'],
 *   'created_range'=>['type'=>'daterange','column'=>'clientes.created_at','start_param'=>'created_from','end_param'=>'created_to'],
 * ]
 */
trait HasDatatableConfig
{
    /**
     * Monta a query com SELECT (aliases), JOINs e filtros; e retorna também o columnsMap.
     *
     * @return array{0: Builder, 1: array}
     */
    public static function makeDatatableQuery(Request $request): array
    {
        /** @var \Illuminate\Database\Eloquent\Model $instance */
        $instance = new static();
        $table    = $instance->getTable();

        $colsCfg  = static::dtColumns();
        $fltCfg   = static::dtFilters();

        // 1) Query base
        /** @var Builder $query */
        $query = static::query();

        // 2) JOINs necessários (sem duplicar)
        $appliedJoins = [];
        foreach ($colsCfg as $alias => $cfg) {
            if (!empty($cfg['join']) && is_array($cfg['join'])) {
                // ['table','left','op','right','type?']
                [$jTable, $left, $op, $right, $type] = $cfg['join'] + [null,null,null,null,'left'];
                $type = $type ?: 'left';
                $key  = implode('|', [$jTable, $left, $op, $right, $type]);
                if (!isset($appliedJoins[$key])) {
                    $query->{$type.'Join'}($jTable, $left, $op, $right);
                    $appliedJoins[$key] = true;
                }
            }
        }

        // 3) SELECT com aliases (ignorando computed)
        $selects = [];
        foreach ($colsCfg as $alias => $cfg) {
            if (!empty($cfg['computed'])) continue;
            if (!empty($cfg['db'])) {
                $selects[] = DB::raw($cfg['db'].' as '.$alias);
            }
        }
        if ($selects) {
            $query->select($selects);
        } else {
            // fallback mínimo
            $pk = property_exists($instance, 'primaryKey') ? $instance->getKeyName() : 'id';
            $query->select(["{$table}.{$pk} as id"]);
        }

        // 4) Filtros conforme configuração
        foreach ($fltCfg as $param => $cfg) {
            $type = $cfg['type'] ?? 'text';
            $val  = $request->query($param);

            $startParam = $cfg['start_param'] ?? ($param.'_from');
            $endParam   = $cfg['end_param']   ?? ($param.'_to');

            switch ($type) {
                case 'text':
                    $q = trim((string) ($val ?? ''));
                    if ($q !== '' && !empty($cfg['columns'])) {
                        $query->where(function ($s) use ($q, $cfg) {
                            foreach ($cfg['columns'] as $i => $col) {
                                $method = $i === 0 ? 'where' : 'orWhere';
                                $s->{$method}($col, 'like', "%{$q}%");
                            }
                        });
                    }
                    break;

                case 'select':
                    if ($val === '' || $val === null) {
                        if (!empty($cfg['nullable'])) {
                            // sem filtro
                            break;
                        }
                    } else {
                        $v = $val;
                        if (($cfg['cast'] ?? null) === 'int')       $v = (int) $v;
                        if (($cfg['transform'] ?? null) === 'upper') $v = strtoupper((string) $v);
                        $op = $cfg['operator'] ?? '=';
                        $query->where($cfg['column'], $op, $v);
                    }
                    break;

                case 'number':
                    if ($val !== '' && $val !== null) {
                        $v  = is_numeric($val) ? $val + 0 : $val;
                        $op = $cfg['operator'] ?? '=';
                        $query->where($cfg['column'], $op, $v);
                    }
                    break;

                case 'date':
                    if ($val) {
                        $query->whereDate($cfg['column'], '=', $val);
                    }
                    break;

                case 'daterange':
                    $from = $request->query($startParam);
                    $to   = $request->query($endParam);
                    if ($from) $query->whereDate($cfg['column'], '>=', $from);
                    if ($to)   $query->whereDate($cfg['column'], '<=', $to);
                    break;
            }
        }

        // 5) columnsMap (alias -> db / order / search)
        $columnsMap = [];
        foreach ($colsCfg as $alias => $cfg) {
            if (!empty($cfg['computed'])) continue;
            $columnsMap[$alias] = [
                'db'     => $cfg['db']     ?? null,
                'order'  => (bool) ($cfg['order']  ?? false),
                'search' => (bool) ($cfg['search'] ?? false),
            ];
        }

        return [$query, $columnsMap];
    }
}
