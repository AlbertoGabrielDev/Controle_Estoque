<?php

namespace Modules\Purchases\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Modules\Purchases\Http\Requests\PurchaseOrderFromQuotationRequest;
use Modules\Purchases\Models\PurchaseOrder;
use Modules\Purchases\Services\PurchaseOrderService;
use RuntimeException;

class PurchaseOrderController extends Controller
{
    public function __construct(private PurchaseOrderService $service)
    {
    }

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

        $query = PurchaseOrder::query()->with('items');

        if ($filters['q'] !== '') {
            $query->where('numero', 'like', '%' . $filters['q'] . '%');
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if ($filters['supplier_id'] !== '') {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if ($filters['data_inicio'] !== '') {
            $query->whereDate('data_emissao', '>=', $filters['data_inicio']);
        }

        if ($filters['data_fim'] !== '') {
            $query->whereDate('data_emissao', '<=', $filters['data_fim']);
        }

        $orders = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return Inertia::render('Purchases/Orders/Index', [
            'filters' => $filters,
            'orders' => $orders,
        ]);
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
