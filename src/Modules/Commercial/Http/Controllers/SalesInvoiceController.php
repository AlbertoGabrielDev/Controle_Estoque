<?php

namespace Modules\Commercial\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Modules\Commercial\Http\Requests\InvoiceStoreRequest;
use Modules\Commercial\Models\CommercialSalesInvoice;
use Modules\Commercial\Repositories\CommercialSalesInvoiceRepository;
use Modules\Commercial\Repositories\CommercialSalesOrderRepository;
use Modules\Commercial\Services\InvoiceService;
use RuntimeException;

class SalesInvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $service,
        private CommercialSalesInvoiceRepository $repository,
        private CommercialSalesOrderRepository $orderRepository,
        private DataTableService $dt,
    ) {
    }

    /**
     * Display a listing of invoices.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request): InertiaResponse
    {
        $filters = [
            'q'          => (string) $request->query('q', ''),
            'status'     => (string) $request->query('status', ''),
            'data_inicio'=> (string) $request->query('data_inicio', ''),
            'data_fim'   => (string) $request->query('data_fim', ''),
        ];

        return Inertia::render('Commercial/Invoices/Index', ['filters' => $filters]);
    }

    /**
     * Return DataTables JSON for invoices.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        [$query, $columnsMap] = CommercialSalesInvoice::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    $showUrl = route('commercial.invoices.show', $row->id);
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
     * Show the form for creating a new invoice for a specific order.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function create(Request $request): InertiaResponse
    {
        $orderId = (int) $request->query('order_id');
        $order   = $this->orderRepository->findWithRelations($orderId);

        return Inertia::render('Commercial/Invoices/Create', [
            'order'            => $order,
            'invoiceable_items'=> $this->repository->invoiceableItemsForOrder($orderId),
        ]);
    }

    /**
     * Store a newly created invoice (partial or full).
     *
     * @param \Modules\Commercial\Http\Requests\InvoiceStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(InvoiceStoreRequest $request): RedirectResponse
    {
        try {
            $data    = $request->validated();
            $invoice = $this->service->createPartialInvoice($data['order_id'], $data['items'], $data);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.invoices.show', $invoice->id)
            ->with('success', 'Fatura emitida com sucesso.');
    }

    /**
     * Display the specified invoice.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function show(int $id): InertiaResponse
    {
        return Inertia::render('Commercial/Invoices/Show', [
            'invoice' => $this->repository->findWithRelations($id),
        ]);
    }

    /**
     * Confirm invoice issuance.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function issue(int $id): RedirectResponse
    {
        try {
            $this->service->issueInvoice($id);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.invoices.show', $id)
            ->with('success', 'Fatura confirmada como emitida.');
    }

    /**
     * Cancel the specified invoice.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(int $id): RedirectResponse
    {
        try {
            $this->service->cancelInvoice($id);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.invoices.show', $id)
            ->with('success', 'Fatura cancelada.');
    }
}
