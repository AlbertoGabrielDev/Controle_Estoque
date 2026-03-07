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
use Modules\Purchases\Http\Requests\PurchaseReceiptStoreRequest;
use Modules\Purchases\Repositories\PurchaseOrderRepository;
use Modules\Purchases\Repositories\PurchaseReceiptRepository;
use Modules\Purchases\Services\PurchaseReceiptService;
use RuntimeException;

class PurchaseReceiptController extends Controller
{
    public function __construct(
        private PurchaseReceiptService $service,
        private PurchaseReceiptRepository $receiptRepository,
        private PurchaseOrderRepository $orderRepository,
        private DataTableService $dt
    ) {
    }

    /**
     * Display a listing of purchase receipts.
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

        return Inertia::render('Purchases/Receipts/Index', [
            'filters' => $filters,
        ]);
    }

    /**
     * Return DataTables JSON for purchase receipts.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        [$query, $columnsMap] = $this->receiptRepository->getDatatableQuery($request->all());

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    $showUrl = route('purchases.receipts.show', $row->id);
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
     * Show the form for creating a new receipt.
     *
     * @return \Inertia\Response
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('Purchases/Receipts/Create', [
            'orders_options' => $this->orderRepository->getAvailableForReceipt(),
        ]);
    }

    /**
     * Store a newly created receipt.
     *
     * @param \Modules\Purchases\Http\Requests\PurchaseReceiptStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PurchaseReceiptStoreRequest $request): RedirectResponse
    {
        try {
            $receipt = $this->service->registerReceipt($request->validated());
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.receipts.show', $receipt->id)
            ->with('success', 'Recebimento registrado com sucesso.');
    }

    /**
     * Display the specified receipt.
     *
     * @param int $receiptId
     * @return \Inertia\Response
     */
    public function show(int $receiptId): InertiaResponse
    {
        $receipt = $this->receiptRepository->findByIdWithRelations($receiptId, [
            'items',
            'order',
            'supplier',
            'returns',
            'payables',
        ]);

        return Inertia::render('Purchases/Receipts/Show', [
            'receipt' => $receipt,
        ]);
    }

    /**
     * Check the specified receipt.
     *
     * @param int $receiptId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function check(int $receiptId): RedirectResponse
    {
        try {
            $this->service->checkReceipt($receiptId);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.receipts.show', $receiptId)
            ->with('success', 'Recebimento conferido com sucesso.');
    }

    /**
     * Accept divergence for the specified receipt.
     *
     * @param int $receiptId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acceptDivergence(int $receiptId): RedirectResponse
    {
        try {
            $this->service->acceptDivergence($receiptId);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.receipts.show', $receiptId)
            ->with('success', 'Divergencia aceita com sucesso.');
    }

    /**
     * Reverse the specified receipt.
     *
     * @param int $receiptId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reverse(int $receiptId): RedirectResponse
    {
        try {
            $this->service->reverseReceipt($receiptId);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.receipts.show', $receiptId)
            ->with('success', 'Recebimento estornado com sucesso.');
    }
}

