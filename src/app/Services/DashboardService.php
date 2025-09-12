<?php

namespace App\Services;

use App\Models\Venda;
use App\Models\Order;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDailySales(int $days = 30): array
    {
        $start = Carbon::today()->subDays($days - 1);
        $end   = Carbon::today();

        $rows = Venda::selectRaw('DATE(created_at) as dia,
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

    public function getTopProducts(int $limit = 5): array
    {
        $rows = Venda::selectRaw('cod_produto, nome_produto,
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
        $rows = Order::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        return [
            'labels' => $rows->pluck('status')->all(),
            'totais' => $rows->pluck('total')->map(fn($v)=>(int)$v)->all(),
        ];
    }

    public function getMonthlySales(?int $year = null): array
    {
        $year = $year ?: (int)date('Y');

        $rows = Venda::selectRaw('MONTH(created_at) as mes,
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
}
