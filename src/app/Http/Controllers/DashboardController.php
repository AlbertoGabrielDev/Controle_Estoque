<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $service) {}

    public function index(Request $request)
    {
        $attendantId = $request->integer('atendente') ?: null;

        // liste os atendentes (ajuste a relação caso use outro pacote/nomes)
        $attendants = User::query()
            ->select('id','name')
            ->whereHas('roles', fn($q) => $q->where('name', 'atendente')) // <= ajuste se seu schema for diferente
            ->orderBy('name')
            ->get();

        $daily    = $this->service->getDailySales(30, $attendantId);
        $topProd  = $this->service->getTopProducts(5, $attendantId);
        $byStatus = $this->service->getOrdersByStatus();
        $monthly  = $this->service->getMonthlySales((int)date('Y'), $attendantId);
        $byUnit   = $this->service->getSalesByUnit($attendantId);
        $kpis     = $this->service->getTodayKpis($attendantId);

        return Inertia::render('Dashboard/SalesDashboard', [
            'daily'       => $daily,
            'topProd'     => $topProd,
            'byStatus'    => $byStatus,
            'monthly'     => $monthly,
            'byUnit'      => $byUnit,
            'kpis'        => $kpis,
            'unidade'     => optional(current_unidade())->nome,
            'attendants'  => $attendants,
            'attendantId' => $attendantId, 
        ]);
    }
}
