<?php

namespace Modules\Purchases\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Modules\Purchases\Models\PurchasePayable;

class PurchasePayableController extends Controller
{
    /**
     * Display a listing of purchase payables.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request): InertiaResponse
    {
        $filters = [
            'q' => (string) $request->query('q', ''),
            'status' => (string) $request->query('status', ''),
            'supplier_id' => (string) $request->query('supplier_id', ''),
            'data_inicio' => (string) $request->query('data_inicio', ''),
            'data_fim' => (string) $request->query('data_fim', ''),
        ];

        $query = PurchasePayable::query();

        if ($filters['q'] !== '') {
            $query->where('numero_documento', 'like', '%' . $filters['q'] . '%');
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if ($filters['supplier_id'] !== '') {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if ($filters['data_inicio'] !== '') {
            $query->whereDate('data_vencimento', '>=', $filters['data_inicio']);
        }

        if ($filters['data_fim'] !== '') {
            $query->whereDate('data_vencimento', '<=', $filters['data_fim']);
        }

        $payables = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return Inertia::render('Purchases/Payables/Index', [
            'filters' => $filters,
            'payables' => $payables,
        ]);
    }

    /**
     * Display the specified payable.
     *
     * @param int $payableId
     * @return \Inertia\Response
     */
    public function show(int $payableId): InertiaResponse
    {
        $payable = PurchasePayable::query()
            ->with(['supplier', 'order', 'receipt'])
            ->findOrFail($payableId);

        return Inertia::render('Purchases/Payables/Show', [
            'payable' => $payable,
        ]);
    }
}
