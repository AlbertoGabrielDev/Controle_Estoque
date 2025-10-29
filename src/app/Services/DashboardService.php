<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Venda;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class DashboardService
{

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

        // Garantia de limites de dia
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

    public function getDailySales(int $days = 30, ?int $userId = null, ?Carbon $from = null, ?Carbon $to = null): array
    {
        [$from, $to] = $this->normalizeRange($from, $to, $days);

        $rows = $this->vendasBase($userId)
            ->selectRaw('DATE(created_at) as dia,
                         SUM(quantidade * preco_venda) as total,
                         SUM(quantidade) as qtd')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        $labels = [];
        $totais = [];
        $qtds   = [];

        foreach (CarbonPeriod::create($from, $to) as $date) {
            $d = $date->toDateString();
            $labels[] = $date->format('d/m');
            $totais[] = isset($rows[$d]) ? (float)$rows[$d]->total : 0.0;
            $qtds[]   = isset($rows[$d]) ? (int)$rows[$d]->qtd   : 0;
        }

        return compact('labels', 'totais', 'qtds');
    }

    public function getTopProducts(int $limit = 5, ?int $userId = null, ?Carbon $from = null, ?Carbon $to = null): array
    {
        [$from, $to] = $this->normalizeRange($from, $to);

        $rows = $this->vendasBase($userId)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('cod_produto, nome_produto,
                         SUM(quantidade * preco_venda) as total,
                         SUM(quantidade) as qtd')
            ->groupBy('cod_produto','nome_produto')
            ->orderByDesc(DB::raw('SUM(quantidade * preco_venda)'))
            ->limit($limit)
            ->get();

        return [
            'labels' => $rows->pluck('nome_produto')->all(),
            'totais' => $rows->pluck('total')->map(fn($v)=>(float)$v)->all(),
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
                         SUM(quantidade * preco_venda) as total')
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
            $totais[] = isset($byKey[$key]) ? (float)$byKey[$key]->total : 0.0;
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

        $rows = $q->selectRaw('u.nome as unidade, SUM(v.quantidade * v.preco_venda) as total')
            ->groupBy('u.nome')
            ->orderByDesc(DB::raw('SUM(v.quantidade * v.preco_venda)'))
            ->get();

        return [
            'labels' => $rows->pluck('unidade')->all(),
            'totais' => $rows->pluck('total')->map(fn($v)=>(float)$v)->all(),
        ];
    }

    public function getKpis(?int $userId = null, ?Carbon $from = null, ?Carbon $to = null): array
    {
        [$from, $to] = $this->normalizeRange($from, $to, 1); // considera hoje se não vierem datas

        $salesCount = $this->vendasBase($userId)
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $revenue = (float) $this->vendasBase($userId)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('COALESCE(SUM(quantidade * preco_venda),0) AS total')
            ->value('total');

        $profit = (float) DB::table('vendas AS v')
            ->join('estoques AS e', 'e.id_produto_fk', '=', 'v.id_produto_fk')
            ->when($userId, fn($qq) => $qq->where('v.id_usuario_fk', $userId))
            ->whereBetween('v.created_at', [$from, $to])
            ->selectRaw('COALESCE(SUM( (v.preco_venda - e.preco_custo) * v.quantidade ),0) AS lucro')
            ->value('lucro');

        return [
            'salesCount' => (int) $salesCount,
            'revenue'    => $revenue,
            'profit'     => $profit,
        ];
    }
}
