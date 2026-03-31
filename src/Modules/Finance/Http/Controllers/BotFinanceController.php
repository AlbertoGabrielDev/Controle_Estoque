<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Customers\Models\Cliente;
use Modules\Finance\Models\Despesa;

class BotFinanceController
{
    /**
     * Saldo / pendências financeiras de um cliente por documento.
     *
     * GET /api/bot/finance/customer-balance?cpf=12345678900
     *
     * Nota: no momento este endpoint retorna despesas associadas ao fornecedor
     * que corresponde ao documento do cliente (cenário B2B). Para B2C puro,
     * o modelo financeiro pode precisar de evolução futura.
     */
    public function customerBalance(Request $request): JsonResponse
    {
        $request->validate([
            'cpf' => 'required|string|min:8|max:20',
        ]);

        $doc = preg_replace('/\D/', '', $request->input('cpf'));

        $customer = Cliente::query()
            ->where('ativo', true)
            ->where('documento', $doc)
            ->first();

        if (! $customer) {
            return response()->json([
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

        return response()->json([
            'found'         => true,
            'customer_name' => $customer->nome ?: $customer->nome_fantasia,
            'credit_limit'  => (float) $customer->limite_credito,
            'blocked'       => (bool) $customer->bloqueado,
            'recent_total'  => round($totalPurchases, 2),
            'recent_count'  => $recentSales->count(),
        ]);
    }
}
