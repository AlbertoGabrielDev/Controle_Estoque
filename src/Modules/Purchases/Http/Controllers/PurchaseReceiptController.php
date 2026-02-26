<?php

namespace Modules\Purchases\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Modules\Purchases\Http\Requests\PurchaseReceiptStoreRequest;
use Modules\Purchases\Models\PurchaseReceipt;
use Modules\Purchases\Services\PurchaseReceiptService;
use RuntimeException;

class PurchaseReceiptController extends Controller
{
    public function __construct(private PurchaseReceiptService $service)
    {
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

        $query = PurchaseReceipt::query()->with('items');

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
            $query->whereDate('data_recebimento', '>=', $filters['data_inicio']);
        }

        if ($filters['data_fim'] !== '') {
            $query->whereDate('data_recebimento', '<=', $filters['data_fim']);
        }

        $receipts = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return Inertia::render('Purchases/Receipts/Index', [
            'filters' => $filters,
            'receipts' => $receipts,
        ]);
    }

    /**
     * Show the form for creating a new receipt.
     *
     * @return \Inertia\Response
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('Purchases/Receipts/Create');
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
        $receipt = PurchaseReceipt::query()
            ->with(['items', 'order', 'supplier', 'returns', 'payables'])
            ->findOrFail($receiptId);

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
