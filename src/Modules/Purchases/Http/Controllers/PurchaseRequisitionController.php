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
use Modules\Purchases\Http\Requests\PurchaseRequisitionStoreRequest;
use Modules\Purchases\Http\Requests\PurchaseRequisitionUpdateRequest;
use Modules\Purchases\Models\PurchaseRequisition;
use Modules\Purchases\Services\PurchaseRequisitionService;
use RuntimeException;

class PurchaseRequisitionController extends Controller
{
    public function __construct(
        private PurchaseRequisitionService $service,
        private DataTableService $dt
    ) {}

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

        return Inertia::render('Purchases/Requisitions/Index', [
            'filters' => $filters,
        ]);
    }

    /**
     * Return DataTables JSON for purchase requisitions.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        [$query, $columnsMap] = PurchaseRequisition::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    $showUrl = route('purchases.requisitions.show', $row->id);
                    $show = sprintf(
                        '<a href="%s" class="p-2 text-blue-600 hover:bg-blue-50 rounded-md inline-flex items-center" title="Ver"><i class="fas fa-eye"></i></a>',
                        e($showUrl)
                    );
                    $canEdit = (string) $row->c2 === 'draft';

                    return DataTableActions::wrap([
                        $show,
                        DataTableActions::edit('purchases.requisitions.edit', $row->id, $canEdit),
                    ]);
                });
            }
        );
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
