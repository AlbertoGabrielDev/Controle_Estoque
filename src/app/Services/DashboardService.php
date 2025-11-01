<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Venda;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Normaliza o intervalo [from, to].
     * - Se vier só from: to = hoje 23:59
     * - Se vier só to: from = (to - fallbackDays + 1) 00:00
     * - Se não vier nada: últimos fallbackDays dias
     */
    private function normalizeRange(?Carbon $from, ?Carbon $to, int $fallbackDays = 30): array
    {
        $todayEnd = Carbon::today()->endOfDay();

        if ($from && !$to) {
            $to = $todayEnd;
        } elseif (!$from && $to) {
            $from = $to->copy()->startOfDay()->subDays($fallbackDays - 1);
        } elseif (!$from && !$to) {
            $to = $todayEnd;
            $from = Carbon::today()->subDays($fallbackDays - 1)->startOfDay();
        }

        // Garantir limites de dia
        $from = $from->copy()->startOfDay();
        $to   = $to->copy()->endOfDay();

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to];
    }

    private function vendasBase(?int $userId)
    {
        $q = Venda::query();
        if ($userId) {
            $q->where('id_usuario_fk', $userId);
        }
        return $q;
    }

    /**
     * Helper: expressão SQL da alíquota do JSON de impostos do estoque.
     * Retorna DECIMAL (coalesce para 0).
     */
    private function aliqExpr(): string
    {
        // MySQL 8+: JSON_UNQUOTE(JSON_EXTRACT(...)) + 0 converte para número
        return "COALESCE((JSON_UNQUOTE(JSON_EXTRACT(e.impostos_json, '$.aliquota')) + 0), 0)";
    }

    /** -------- GRÁFICOS -------- */

    public function getDailySales(int $days = 30, ?int $userId = null, ?Carbon $from = null, ?Carbon $to = null): array
    {
        [$from, $to] = $this->normalizeRange($from, $to, $days);

        $rows = $this->vendasBase($userId)
            ->selectRaw('DATE(created_at) dia,
                         SUM(preco_venda)       AS total_bruto,
                         SUM(quantidade)        AS qtd')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        $labels = [];
        $totais = []; // total bruto por dia (para manter o gráfico atual de faturamento)
        $qtds   = [];

        foreach (CarbonPeriod::create($from, $to) as $date) {
            $d = $date->toDateString();
            $labels[] = $date->format('d/m');
            $totais[] = isset($rows[$d]) ? (float)$rows[$d]->total_bruto : 0.0;
            $qtds[]   = isset($rows[$d]) ? (int)$rows[$d]->qtd        : 0;
        }

        return compact('labels', 'totais', 'qtds');
    }

    public function getTopProducts(int $limit = 5, ?int $userId = null, ?Carbon $from = null, ?Carbon $to = null): array
    {
        [$from, $to] = $this->normalizeRange($from, $to);

        $rows = $this->vendasBase($userId)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('cod_produto, nome_produto,
                         SUM(preco_venda) AS total_bruto,
                         SUM(quantidade)  AS qtd')
            ->groupBy('cod_produto','nome_produto')
            ->orderByDesc(DB::raw('SUM(preco_venda)'))
            ->limit($limit)
            ->get();

        return [
            'labels' => $rows->pluck('nome_produto')->all(),
            'totais' => $rows->pluck('total_bruto')->map(fn($v)=>(float)$v)->all(),
            'qtds'   => $rows->pluck('qtd')->map(fn($v)=>(int)$v)->all(),
        ];
    }

    public function getOrdersByStatus(?Carbon $from = null, ?Carbon $to = null): array
    {
        [$from, $to] = $this->normalizeRange($from, $to);

        $rows = Cart::selectRaw('status, COUNT(*) as total')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('status')
            ->get();

        return [
            'labels' => $rows->pluck('status')->all(),
            'totais' => $rows->pluck('total')->map(fn($v)=>(int)$v)->all(),
        ];
    }

    public function getMonthlySales(?int $year = null, ?int $userId = null, ?Carbon $from = null, ?Carbon $to = null): array
    {
        [$from, $to] = $this->normalizeRange($from, $to);

        $rows = $this->vendasBase($userId)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('YEAR(created_at) as ano, MONTH(created_at) as mes,
                         SUM(preco_venda)  as total_bruto')
            ->groupBy('ano','mes')
            ->orderBy('ano')->orderBy('mes')
            ->get();

        $labels = [];
        $totais = [];
        $byKey = $rows->keyBy(fn($r) => sprintf('%04d-%02d', $r->ano, $r->mes));

        $cursor = $from->copy()->startOfMonth();
        $limit  = $to->copy()->startOfMonth();
        while ($cursor <= $limit) {
            $key = $cursor->format('Y-m');
            $labels[] = $cursor->locale('pt_BR')->isoFormat('MMM/YY');
            $totais[] = isset($byKey[$key]) ? (float)$byKey[$key]->total_bruto : 0.0;
            $cursor->addMonth();
        }

        return ['labels' => $labels, 'totais' => $totais, 'year' => $from->year === $to->year ? $from->year : null];
    }

    public function getSalesByUnit(?int $userId = null, ?Carbon $from = null, ?Carbon $to = null): array
    {
        [$from, $to] = $this->normalizeRange($from, $to);

        $q = DB::table('vendas as v')
            ->join('unidades as u', 'u.id_unidade', '=', 'v.id_unidade_fk')
            ->whereBetween('v.created_at', [$from, $to]);

        if ($userId) $q->where('v.id_usuario_fk', $userId);

        $rows = $q->selectRaw('u.nome as unidade, SUM(v.preco_venda) as total_bruto')
            ->groupBy('u.nome')
            ->orderByDesc(DB::raw('SUM(v.preco_venda)'))
            ->get();

        return [
            'labels' => $rows->pluck('unidade')->all(),
            'totais' => $rows->pluck('total_bruto')->map(fn($v)=>(float)$v)->all(),
        ];
    }

    /** -------- KPIs -------- */

    public function getKpis(?int $userId = null, ?Carbon $from = null, ?Carbon $to = null): array
    {
        [$from, $to] = $this->normalizeRange($from, $to, 1); // hoje por padrão

        // 1) Contagem de vendas
        $salesCount = $this->vendasBase($userId)
            ->whereBetween('created_at', [$from, $to])
            ->count();

        // 2) FATURAMENTO BRUTO: soma dos totais de linha
        $grossRevenue = (float) $this->vendasBase($userId)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('COALESCE(SUM(preco_venda),0) AS total')
            ->value('total');

        // 3) IMPOSTOS sobre vendas (por linha, via id_estoque_fk → aliquota no JSON)
        //    liquido_da_linha = total_da_linha * (1 - aliquota)
        //    impostos_da_linha = total_da_linha * aliquota
        $aliq = $this->aliqExpr();

        $taxes = (float) DB::table('vendas as v')
            ->leftJoin('estoques as e', 'e.id_estoque', '=', 'v.id_estoque_fk')
            ->when($userId, fn($qq) => $qq->where('v.id_usuario_fk', $userId))
            ->whereBetween('v.created_at', [$from, $to])
            ->selectRaw("COALESCE(SUM(v.preco_venda * ($aliq)), 0) as impostos")
            ->value('impostos');

        // 4) FATURAMENTO LÍQUIDO
        $netRevenue = max(0.0, $grossRevenue - $taxes);

        // 5) LUCRO (usando custo unitário do estoque)
        //    preco_unit = total_linha / quantidade
        $profit = (float) DB::table('vendas AS v')
            ->leftJoin('estoques AS e', 'e.id_estoque', '=', 'v.id_estoque_fk')
            ->when($userId, fn($qq) => $qq->where('v.id_usuario_fk', $userId))
            ->whereBetween('v.created_at', [$from, $to])
            ->selectRaw("
                COALESCE(SUM( ((v.preco_venda / NULLIF(v.quantidade,0)) - e.preco_custo) * v.quantidade ), 0
            ) AS lucro")
            ->value('lucro');

        return [
            'salesCount'   => (int) $salesCount,
            'grossRevenue' => $grossRevenue,
            'netRevenue'   => $netRevenue,
            'taxes'        => $taxes,
            'profit'       => $profit,
        ];
    }
}
