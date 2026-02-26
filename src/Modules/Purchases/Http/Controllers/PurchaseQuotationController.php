<?php

namespace Modules\Purchases\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    public function __construct(
        private PurchaseQuotationService $service,
        private DataTableService $dt
    ) {}

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

        return Inertia::render('Purchases/Quotations/Index', [
            'filters' => $filters,
        ]);
    }

    /**
     * Return DataTables JSON for purchase quotations.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        [$query, $columnsMap] = PurchaseQuotation::makeDatatableQuery($request);

        $supplierId = (string) $request->query('supplier_id', '');
        if ($supplierId !== '') {
            $query->whereExists(function ($sub) use ($supplierId) {
                $sub->select(DB::raw(1))
                    ->from('purchase_quotation_suppliers as pqs')
                    ->whereColumn('pqs.quotation_id', 'purchase_quotations.id')
                    ->where('pqs.supplier_id', $supplierId);
            });
        }

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    $showUrl = route('purchases.quotations.show', $row->id);
                    $show = sprintf(
                        '<a href="%s" class="p-2 text-blue-600 hover:bg-blue-50 rounded-md inline-flex items-center" title="Ver"><i class="fas fa-eye"></i></a>',
                        e($showUrl)
                    );
                    $canEdit = (string) $row->c2 === 'aberta';

                    return DataTableActions::wrap([
                        $show,
                        DataTableActions::edit('purchases.quotations.edit', $row->id, $canEdit),
                    ]);
                });
            }
        );
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
