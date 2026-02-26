<?php

namespace Modules\Purchases\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Modules\Purchases\Http\Requests\PurchaseRequisitionStoreRequest;
use Modules\Purchases\Http\Requests\PurchaseRequisitionUpdateRequest;
use Modules\Purchases\Models\PurchaseRequisition;
use Modules\Purchases\Services\PurchaseRequisitionService;
use RuntimeException;

class PurchaseRequisitionController extends Controller
{
    public function __construct(private PurchaseRequisitionService $service)
    {
    }

    /**
     * Display a listing of purchase requisitions.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request): InertiaResponse
    {
        $filters = [
            'q' => (string) $request->query('q', ''),
            'status' => (string) $request->query('status', ''),
            'data_inicio' => (string) $request->query('data_inicio', ''),
            'data_fim' => (string) $request->query('data_fim', ''),
        ];

        $query = PurchaseRequisition::query()->with('items');

        if ($filters['q'] !== '') {
            $query->where('numero', 'like', '%' . $filters['q'] . '%');
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if ($filters['data_inicio'] !== '') {
            $query->whereDate('data_requisicao', '>=', $filters['data_inicio']);
        }

        if ($filters['data_fim'] !== '') {
            $query->whereDate('data_requisicao', '<=', $filters['data_fim']);
        }

        $requisitions = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return Inertia::render('Purchases/Requisitions/Index', [
            'filters' => $filters,
            'requisitions' => $requisitions,
        ]);
    }

    /**
     * Show the form for creating a new requisition.
     *
     * @return \Inertia\Response
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('Purchases/Requisitions/Create');
    }

    /**
     * Store a newly created requisition.
     *
     * @param \Modules\Purchases\Http\Requests\PurchaseRequisitionStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PurchaseRequisitionStoreRequest $request): RedirectResponse
    {
        try {
            $requisition = $this->service->createRequisition($request->validated(), auth()->id());
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.requisitions.show', $requisition->id)
            ->with('success', 'Requisicao criada com sucesso.');
    }

    /**
     * Display the specified requisition.
     *
     * @param int $requisitionId
     * @return \Inertia\Response
     */
    public function show(int $requisitionId): InertiaResponse
    {
        $requisition = PurchaseRequisition::query()
            ->with(['items', 'quotations.orders'])
            ->findOrFail($requisitionId);

        return Inertia::render('Purchases/Requisitions/Show', [
            'requisition' => $requisition,
        ]);
    }

    /**
     * Show the form for editing the specified requisition.
     *
     * @param int $requisitionId
     * @return \Inertia\Response
     */
    public function edit(int $requisitionId): InertiaResponse
    {
        $requisition = PurchaseRequisition::query()
            ->with('items')
            ->findOrFail($requisitionId);

        return Inertia::render('Purchases/Requisitions/Edit', [
            'requisition' => $requisition,
        ]);
    }

    /**
     * Update the specified requisition.
     *
     * @param \Modules\Purchases\Http\Requests\PurchaseRequisitionUpdateRequest $request
     * @param int $requisitionId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PurchaseRequisitionUpdateRequest $request, int $requisitionId): RedirectResponse
    {
        try {
            $this->service->updateRequisition($requisitionId, $request->validated());
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.requisitions.show', $requisitionId)
            ->with('success', 'Requisicao atualizada com sucesso.');
    }

    /**
     * Approve the specified requisition.
     *
     * @param int $requisitionId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(int $requisitionId): RedirectResponse
    {
        try {
            $this->service->approveRequisition($requisitionId);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.requisitions.show', $requisitionId)
            ->with('success', 'Requisicao aprovada com sucesso.');
    }

    /**
     * Cancel the specified requisition.
     *
     * @param int $requisitionId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(int $requisitionId): RedirectResponse
    {
        try {
            $this->service->cancelRequisition($requisitionId);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.requisitions.show', $requisitionId)
            ->with('success', 'Requisicao cancelada com sucesso.');
    }

    /**
     * Close the specified requisition.
     *
     * @param int $requisitionId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function close(int $requisitionId): RedirectResponse
    {
        try {
            $this->service->closeRequisition($requisitionId);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.requisitions.show', $requisitionId)
            ->with('success', 'Requisicao fechada com sucesso.');
    }
}
