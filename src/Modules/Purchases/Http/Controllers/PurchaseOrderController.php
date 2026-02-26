<?php

namespace Modules\Purchases\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Modules\Purchases\Http\Requests\PurchaseOrderFromQuotationRequest;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Services\PurchaseOrderService;
use RuntimeException;

class PurchaseOrderController extends Controller
{
    public function __construct(
        private PurchaseOrderService $service,
        private DataTableService $dt
    ) {}

    /**
     * Display a listing of purchase orders.
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

        return Inertia::render('Purchases/Orders/Index', [
            'filters' => $filters,
        ]);
    }

    /**
     * Return DataTables JSON for purchase orders.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        [$query, $columnsMap] = PurchaseOrder::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    $showUrl = route('purchases.orders.show', $row->id);
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
     * Display the specified order.
     *
     * @param int $orderId
     * @return \Inertia\Response
     */
    public function show(int $orderId): InertiaResponse
    {
        $order = PurchaseOrder::query()
            ->with(['items', 'receipts', 'supplier', 'quotation', 'returns', 'payables'])
            ->findOrFail($orderId);

        return Inertia::render('Purchases/Orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Create orders from a closed quotation.
     *
     * @param \Modules\Purchases\Http\Requests\PurchaseOrderFromQuotationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeFromQuotation(PurchaseOrderFromQuotationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $orders = $this->service->createFromQuotation((int) $data['quotation_id']);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        $firstOrder = $orders[0] ?? null;

        if ($firstOrder) {
            return redirect()->route('purchases.orders.show', $firstOrder->id)
                ->with('success', 'Pedidos gerados com sucesso.');
        }

        return redirect()->route('purchases.orders.index')
            ->with('success', 'Pedidos gerados com sucesso.');
    }

    /**
     * Cancel the specified order.
     *
     * @param int $orderId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(int $orderId): RedirectResponse
    {
        try {
            $this->service->cancelOrder($orderId);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.orders.show', $orderId)
            ->with('success', 'Pedido cancelado com sucesso.');
    }

    /**
     * Close the specified order.
     *
     * @param int $orderId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function close(int $orderId): RedirectResponse
    {
        try {
            $this->service->closeOrder($orderId);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.orders.show', $orderId)
            ->with('success', 'Pedido fechado com sucesso.');
    }
}
