<?php

namespace Modules\Purchases\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Modules\Purchases\Http\Requests\PurchaseQuotationAddSupplierRequest;
use Modules\Purchases\Http\Requests\PurchaseQuotationPricesRequest;
use Modules\Purchases\Http\Requests\PurchaseQuotationSelectItemRequest;
use Modules\Purchases\Http\Requests\PurchaseQuotationStoreRequest;
use Modules\Purchases\Http\Requests\PurchaseQuotationUpdateRequest;
use Modules\Purchases\Models\PurchaseQuotation;
use Modules\Purchases\Services\PurchaseQuotationService;
use RuntimeException;

class PurchaseQuotationController extends Controller
{
    public function __construct(private PurchaseQuotationService $service)
    {
    }

    /**
     * Display a listing of purchase quotations.
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

        $query = PurchaseQuotation::query()->with('requisition');

        if ($filters['q'] !== '') {
            $query->where('numero', 'like', '%' . $filters['q'] . '%');
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if ($filters['supplier_id'] !== '') {
            $query->whereHas('suppliers', function ($supplierQuery) use ($filters) {
                $supplierQuery->where('supplier_id', $filters['supplier_id']);
            });
        }

        if ($filters['data_inicio'] !== '') {
            $query->whereDate('data_limite', '>=', $filters['data_inicio']);
        }

        if ($filters['data_fim'] !== '') {
            $query->whereDate('data_limite', '<=', $filters['data_fim']);
        }

        $quotations = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return Inertia::render('Purchases/Quotations/Index', [
            'filters' => $filters,
            'quotations' => $quotations,
        ]);
    }

    /**
     * Show the form for creating a new quotation.
     *
     * @return \Inertia\Response
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('Purchases/Quotations/Create');
    }

    /**
     * Store a newly created quotation.
     *
     * @param \Modules\Purchases\Http\Requests\PurchaseQuotationStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PurchaseQuotationStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $quotation = $this->service->createFromRequisition((int) $data['requisition_id'], $data);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.quotations.show', $quotation->id)
            ->with('success', 'Cotacao criada com sucesso.');
    }

    /**
     * Display the specified quotation.
     *
     * @param int $quotationId
     * @return \Inertia\Response
     */
    public function show(int $quotationId): InertiaResponse
    {
        $quotation = PurchaseQuotation::query()
            ->with(['requisition.items', 'suppliers.items', 'suppliers.supplier', 'orders'])
            ->findOrFail($quotationId);

        return Inertia::render('Purchases/Quotations/Show', [
            'quotation' => $quotation,
        ]);
    }

    /**
     * Show the form for editing the specified quotation.
     *
     * @param int $quotationId
     * @return \Inertia\Response
     */
    public function edit(int $quotationId): InertiaResponse
    {
        $quotation = PurchaseQuotation::query()
            ->with(['requisition.items', 'suppliers.items', 'suppliers.supplier', 'orders'])
            ->findOrFail($quotationId);

        return Inertia::render('Purchases/Quotations/Edit', [
            'quotation' => $quotation,
        ]);
    }

    /**
     * Update the specified quotation.
     *
     * @param \Modules\Purchases\Http\Requests\PurchaseQuotationUpdateRequest $request
     * @param int $quotationId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PurchaseQuotationUpdateRequest $request, int $quotationId): RedirectResponse
    {
        try {
            $this->service->updateQuotation($quotationId, $request->validated());
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.quotations.show', $quotationId)
            ->with('success', 'Cotacao atualizada com sucesso.');
    }

    /**
     * Add a supplier to the quotation.
     *
     * @param \Modules\Purchases\Http\Requests\PurchaseQuotationAddSupplierRequest $request
     * @param int $quotationId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addSupplier(PurchaseQuotationAddSupplierRequest $request, int $quotationId): RedirectResponse
    {
        try {
            $this->service->addSupplier($quotationId, (int) $request->validated()['supplier_id']);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.quotations.show', $quotationId)
            ->with('success', 'Fornecedor adicionado com sucesso.');
    }

    /**
     * Register supplier prices for quotation items.
     *
     * @param \Modules\Purchases\Http\Requests\PurchaseQuotationPricesRequest $request
     * @param int $quotationId
     * @param int $quotationSupplierId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registerPrices(
        PurchaseQuotationPricesRequest $request,
        int $quotationId,
        int $quotationSupplierId
    ): RedirectResponse {
        try {
            $this->service->registerSupplierPrices($quotationSupplierId, $request->validated()['items']);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.quotations.show', $quotationId)
            ->with('success', 'Precos registrados com sucesso.');
    }

    /**
     * Select a winning supplier item for the quotation.
     *
     * @param \Modules\Purchases\Http\Requests\PurchaseQuotationSelectItemRequest $request
     * @param int $quotationId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function selectItem(PurchaseQuotationSelectItemRequest $request, int $quotationId): RedirectResponse
    {
        try {
            $this->service->selectWinnerForItem($quotationId, (int) $request->validated()['quotation_supplier_item_id']);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.quotations.show', $quotationId)
            ->with('success', 'Vencedor selecionado com sucesso.');
    }

    /**
     * Close the specified quotation.
     *
     * @param int $quotationId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function close(int $quotationId): RedirectResponse
    {
        try {
            $this->service->closeQuotation($quotationId);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.quotations.show', $quotationId)
            ->with('success', 'Cotacao encerrada com sucesso.');
    }

    /**
     * Cancel the specified quotation.
     *
     * @param int $quotationId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(int $quotationId): RedirectResponse
    {
        try {
            $this->service->cancelQuotation($quotationId);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.quotations.show', $quotationId)
            ->with('success', 'Cotacao cancelada com sucesso.');
    }
}
