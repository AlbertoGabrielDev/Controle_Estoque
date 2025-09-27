<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CalendarController extends Controller
{

    public function index(Request $request)
    {
        $now = now();
        return Inertia::render('Calendar/Calendar', [
            'year' => (int) $now->year,
            'month' => (int) $now->month,
            'fetchEndpoint' => route('api.vendas.month'),
        ]);
    }

    public function month(Request $request)
    {
        $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $year = (int) $request->integer('year');
        $month = (int) $request->integer('month');

        $rows = DB::table('vendas')
            ->selectRaw("
                DATE(vendas.created_at) as dia,
                DATE_FORMAT(vendas.created_at, '%H:%i') as time,
                nome_produto as product,
                quantidade as quantity
            ")
            ->addSelect('unidades.nome as unit')
            ->whereYear('vendas.created_at', $year)
            ->whereMonth('vendas.created_at', $month)
            ->join('unidades', 'vendas.id_unidade_fk', '=', 'unidades.id_unidade')
            ->orderBy('vendas.created_at')
            ->get();

        $salesByDate = [];
        foreach ($rows as $r) {
            $key = (string) $r->dia;
            if (!isset($salesByDate[$key])) {
                $salesByDate[$key] = [];
            }
            $salesByDate[$key][] = [
                'time' => $r->time,
                'product' => (string) $r->product,
                'unit' => (string) $r->unit,
                'quantity' => (float) $r->quantity,
            ];
        }
        return response()->json([
            'salesByDate' => $salesByDate,
            'year' => $year,
            'month' => $month,
        ]);
    }
}
