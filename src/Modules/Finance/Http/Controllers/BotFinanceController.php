<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Bot\BaseBotController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Customers\Models\Cliente;
use Modules\Finance\Models\Despesa;

class BotFinanceController extends BaseBotController
{
    /**
     * Saldo / pendências financeiras de um cliente.
     *
     * GET /api/bot/finance?customer_cpf=12345678900
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'customer_cpf' => 'nullable|string|min:8|max:20',
            'customer_id'  => 'nullable|integer',
        ]);

        $customer = null;

        if ($request->filled('customer_id')) {
            $customer = Cliente::find($request->input('customer_id'));
        } elseif ($request->filled('customer_cpf')) {
            $doc = preg_replace('/\D/', '', $request->input('customer_cpf'));
            $customer = Cliente::query()
                ->where('ativo', true)
                ->where('documento', $doc)
                ->first();
        }

        if (! $customer) {
            return $this->responseSuccess([
                'found'   => false,
                'balance' => null,
            ]);
        }

        // Busca vendas recentes para estimar saldo de compras
        $recentSales = $customer->vendas()
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $totalPurchases = $recentSales->sum(fn ($v) => $v->quantidade * $v->preco_venda);

        return $this->responseSuccess([
            'found'         => true,
            'customer_name' => $customer->nome ?: $customer->nome_fantasia,
            'credit_limit'  => $this->formatCurrency($customer->limite_credito),
            'blocked'       => (bool) $customer->bloqueado,
            'recent_total'  => $this->formatCurrency($totalPurchases),
            'recent_count'  => $recentSales->count(),
        ]);
    }
}
