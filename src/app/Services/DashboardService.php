<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Venda;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    private function vendasQuery(?int $userId)
    {
        $q = Venda::query();
        if ($userId) {
            $q->where('id_usuario_fk', $userId);
        }
        return $q;
    }

    public function getDailySales(int $days = 30, ?int $userId = null): array
    {
        $start = Carbon::today()->subDays($days - 1);
        $end   = Carbon::today();

        $rows = $this->vendasQuery($userId)
            ->selectRaw('DATE(created_at) as dia,
                         SUM(quantidade * preco_venda) as total,
                         SUM(quantidade) as qtd')
            ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        $labels = [];
        $totais = [];
        $qtds   = [];

        foreach (CarbonPeriod::create($start, $end) as $date) {
            $d = $date->toDateString();
            $labels[] = $date->format('d/m');
            $totais[] = isset($rows[$d]) ? (float)$rows[$d]->total : 0.0;
            $qtds[]   = isset($rows[$d]) ? (int)$rows[$d]->qtd   : 0;
        }

        return compact('labels', 'totais', 'qtds');
    }

    public function getTopProducts(int $limit = 5, ?int $userId = null): array
    {
        $rows = $this->vendasQuery($userId)
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

    public function getOrdersByStatus(): array
    {
        $rows = Cart::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        return [
            'labels' => $rows->pluck('status')->all(),
            'totais' => $rows->pluck('total')->map(fn($v)=>(int)$v)->all(),
        ];
    }

    public function getMonthlySales(?int $year = null, ?int $userId = null): array
    {
        $year = $year ?: (int)date('Y');

        $rows = $this->vendasQuery($userId)
            ->selectRaw('MONTH(created_at) as mes,
                         SUM(quantidade * preco_venda) as total')
            ->whereYear('created_at', $year)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->keyBy('mes');

        $labels = [];
        $totais = [];
        for ($m = 1; $m <= 12; $m++) {
            $labels[] = Carbon::createFromDate($year, $m, 1)->locale('pt_BR')->isoFormat('MMM');
            $totais[] = isset($rows[$m]) ? (float)$rows[$m]->total : 0.0;
        }

        return compact('labels', 'totais', 'year');
    }

    public function getSalesByUnit(?int $userId = null): array
    {
        $q = DB::table('vendas as v')
            ->join('unidades as u', 'u.id_unidade', '=', 'v.id_unidade_fk');

        if ($userId) {
            $q->where('v.id_usuario_fk', $userId);
        }

        $rows = $q->selectRaw('u.nome as unidade, SUM(v.quantidade * v.preco_venda) as total')
            ->groupBy('u.nome')
            ->orderByDesc(DB::raw('SUM(v.quantidade * v.preco_venda)'))
            ->get();

        return [
            'labels' => $rows->pluck('unidade')->all(),
            'totais' => $rows->pluck('total')->map(fn($v)=>(float)$v)->all(),
        ];
    }

    public function getTodayKpis(?int $userId = null): array
    {
        $start = Carbon::today()->startOfDay();
        $end   = Carbon::today()->endOfDay();

        $salesCount = $this->vendasQuery($userId)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $revenue = (float) $this->vendasQuery($userId)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('COALESCE(SUM(quantidade * preco_venda),0) AS total')
            ->value('total');

        $profit = (float) DB::table('vendas AS v')
            ->join('estoques AS e', 'e.id_produto_fk', '=', 'v.id_produto_fk') // ajuste se necessário
            ->when($userId, fn($qq) => $qq->where('v.id_usuario_fk', $userId))
            ->whereBetween('v.created_at', [$start, $end])
            ->selectRaw('COALESCE(SUM( (v.preco_venda - e.preco_custo) * v.quantidade ),0) AS lucro')
            ->value('lucro');

        return [
            'salesCount' => (int) $salesCount,
            'revenue'    => $revenue,
            'profit'     => $profit,
        ];
    }
}
