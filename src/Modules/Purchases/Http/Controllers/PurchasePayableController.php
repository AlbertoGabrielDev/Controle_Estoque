<?php

namespace Modules\Purchases\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Modules\Purchases\Models\PurchasePayable;

class PurchasePayableController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param \App\Services\DataTableService $dt
     */
    public function __construct(private DataTableService $dt)
    {
    }

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

        return Inertia::render('Purchases/Payables/Index', [
            'filters' => $filters,
        ]);
    }

    /**
     * Return DataTables JSON for purchase payables.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        [$query, $columnsMap] = PurchasePayable::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    $showUrl = route('purchases.payables.show', $row->id);
                    $show = sprintf(
                        '<a href="%s" class="p-2 text-blue-600 hover:bg-blue-50 rounded-md inline-flex items-center" title="Ver"><i class="fas fa-eye"></i></a>',
                        e($showUrl)
                    );

                    return DataTableActions::wrap([$show]);
                });
            }
        );
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
