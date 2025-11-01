<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $service) {}

    public function index(Request $request)
    {
        $attendantId = $request->integer('atendente') ?: null;
        $fromStr = $request->input('from');
        $toStr   = $request->input('to');
        $from = $fromStr ? Carbon::createFromFormat('Y-m-d', $fromStr)->startOfDay() : null;
        $to   = $toStr   ? Carbon::createFromFormat('Y-m-d', $toStr)->endOfDay()   : null;

        $attendants = User::query()
            ->select('id','name')
            ->whereHas('roles', fn($q) => $q->where('name', 'atendente'))
            ->orderBy('name')
            ->get();

        $daily    = $this->service->getDailySales(30, $attendantId, $from, $to);
        $topProd  = $this->service->getTopProducts(5, $attendantId, $from, $to);
        $byStatus = $this->service->getOrdersByStatus($from, $to);
        $monthly  = $this->service->getMonthlySales(null, $attendantId, $from, $to);
        $byUnit   = $this->service->getSalesByUnit($attendantId, $from, $to);
        $kpis     = $this->service->getKpis($attendantId, $from, $to);

        return Inertia::render('Dashboard/SalesDashboard', [
            'daily'       => $daily,
            'topProd'     => $topProd,
            'byStatus'    => $byStatus,
            'monthly'     => $monthly,
            'byUnit'      => $byUnit,
            'kpis'        => $kpis, // contém: salesCount, grossRevenue, netRevenue, taxes, profit
            'unidade'     => optional(current_unidade())->nome,
            'attendants'  => $attendants,
            'attendantId' => $attendantId,
            'from'        => $from?->toDateString(),
            'to'          => $to?->toDateString(),
        ]);
    }
}
