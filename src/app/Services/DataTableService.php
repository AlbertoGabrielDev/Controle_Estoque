<?php

namespace App\Services;

use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
/**
 * Serviço genérico para construir respostas no formato DataTables (serverSide),
 * usando aliases seguros e mapeando busca/ordenação para colunas reais.
 *
 * Exemplo de $columnsMap:
 * [
 *   'id' => ['db' => 'clientes.id_cliente', 'order' => true,  'search' => false],
 *   'c1' => ['db' => 'clientes.nome',       'order' => true,  'search' => true ],
 *   'c2' => ['db' => 'clientes.documento',  'order' => true,  'search' => true ],
 *   'c3' => ['db' => 'clientes.whatsapp',   'order' => false, 'search' => true ],
 *   'c4' => ['db' => 'clientes.uf',         'order' => true,  'search' => true ],
 *   'seg'=> ['db' => 'customer_segments.nome', 'order' => false, 'search' => true ],
 *   'st' => ['db' => 'clientes.status',     'order' => true,  'search' => false],
 * ]
 */
class DataTableService
{
    /**
     * Constrói a resposta JSON do DataTables a partir de uma query Eloquent e um mapa de colunas.
     *
     * @param Builder $query         Query Eloquent já com SELECT dos aliases (id, c1, c2, ...)
     * @param array   $columnsMap    ['alias'=> ['db'=> 'tabela.coluna', 'order'=>bool, 'search'=>bool], ...]
     * @param array   $rawColumns    Colunas que contêm HTML e não devem ser escapadas (ex.: ['acoes'])
     * @param callable|null $decorate Callback opcional: function (DataTableAbstract $dt) { ...addColumn('acoes', fn($row)=>'...'); }
     * @return JsonResponse
     */
     public function make(
        Builder $baseQuery,
        array $columnsMap,            // ['alias' => ['db'=>'t.col','order'=>bool,'search'=>bool], ...]
        array $rawColumns = [],
        callable $decorate = null
    ): JsonResponse {
        $request = app(Request::class);

        /**
         * 1) MUTAR A REQUEST: alias -> db
         *    - preenche columns[*].name com a coluna REAL do BD (map)
         *    - força columns[*].searchable/orderable conforme $columnsMap
         *    Isso evita que o Yajra tente usar 'clientes.c1' etc.
         */
        $mut = $request->all();
        if (!empty($mut['columns']) && is_array($mut['columns'])) {
            foreach ($mut['columns'] as $i => $col) {
                $alias = $col['data'] ?? null; // ex.: 'c1','seg','st'
                if ($alias && isset($columnsMap[$alias])) {
                    $cfg = $columnsMap[$alias];
                    // coluna real no BD
                    $mut['columns'][$i]['name']       = $cfg['db'] ?? '';
                    // flags
                    $mut['columns'][$i]['searchable'] = ($cfg['search'] ?? false) ? 'true' : 'false';
                    $mut['columns'][$i]['orderable']  = ($cfg['order']  ?? false) ? 'true' : 'false';
                } else {
                    // sem mapeamento => desabilita
                    $mut['columns'][$i]['name']       = '';
                    $mut['columns'][$i]['searchable'] = 'false';
                    $mut['columns'][$i]['orderable']  = 'false';
                }
            }
            // sobrepõe a request (apenas no servidor)
            $request->merge($mut);
        }

        /**
         * 2) Cria o DataTable
         */
        $dt = DataTables::eloquent($baseQuery);

        /**
         * 3) (Opcional) se quiser substituir a busca global padrão do Yajra por uma sua,
         *    descomente o bloco abaixo. Como já setamos columns[*].name/searchable,
         *    você pode também confiar na busca global padrão do Yajra e remover este bloco.
         */
        $searchValue = trim((string) $request->input('search.value', ''));
        if ($searchValue !== '') {
            $dt->filter(function (Builder $query) use ($columnsMap, $searchValue) {
                $kw = mb_strtolower($searchValue);
                $query->where(function ($w) use ($columnsMap, $kw) {
                    foreach ($columnsMap as $alias => $cfg) {
                        if (empty($cfg['search']) || empty($cfg['db'])) continue;
                        $w->orWhere(DB::raw("LOWER({$cfg['db']})"), 'like', "%{$kw}%");
                    }
                });
            }, true);
        }

        /**
         * 4) Colunas raw (HTML)
         */
        if ($rawColumns) {
            $dt->rawColumns($rawColumns);
        }

        /**
         * 5) Hook para colunas calculadas (ex.: 'acoes')
         */
        if ($decorate) {
            $decorate($dt);
        }

        return $dt->toJson();
    }
}
