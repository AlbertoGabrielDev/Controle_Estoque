<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $service) {}

    public function index()
    {
        $daily    = $this->service->getDailySales(30);
        $topProd  = $this->service->getTopProducts(5);
        $byStatus = $this->service->getOrdersByStatus();
        $monthly  = $this->service->getMonthlySales((int)date('Y'));

        return Inertia::render('Dashboard/SalesDashboard', [
            'daily'    => $daily,
            'topProd'  => $topProd,
            'byStatus' => $byStatus,
            'monthly'  => $monthly,
            // Opcional: mostrar unidade atual no cabeçalho
            'unidade'  => optional(current_unidade())->nome,
        ]);
    }
}
