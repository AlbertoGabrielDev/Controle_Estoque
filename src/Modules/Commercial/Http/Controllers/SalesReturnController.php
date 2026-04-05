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
use Modules\Commercial\Http\Requests\SalesReturnStoreRequest;
use Modules\Commercial\Models\CommercialSalesReturn;
use Modules\Commercial\Repositories\CommercialSalesReturnRepository;
use Modules\Commercial\Services\SalesReturnService;
use RuntimeException;

class SalesReturnController extends Controller
{
    public function __construct(
        private SalesReturnService $service,
        private CommercialSalesReturnRepository $repository,
        private DataTableService $dt,
    ) {
    }

    /**
     * Display a listing of returns.
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

        return Inertia::render('Commercial/Returns/Index', ['filters' => $filters]);
    }

    /**
     * Return DataTables JSON for returns.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        [$query, $columnsMap] = CommercialSalesReturn::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    $showUrl = route('commercial.returns.show', $row->id);
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
     * Show the form for creating a new return.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function create(Request $request): InertiaResponse
    {
        $payload = [];

        if ($invoiceId = $request->query('invoice_id')) {
            $payload['invoice_id'] = (int) $invoiceId;
            $payload['returnable_items'] = $this->repository->returnableItemsForInvoice((int) $invoiceId);
        }

        return Inertia::render('Commercial/Returns/Create', $payload);
    }

    /**
     * Store a newly created return.
     *
     * @param \Modules\Commercial\Http\Requests\SalesReturnStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SalesReturnStoreRequest $request): RedirectResponse
    {
        try {
            $return = $this->service->createReturn($request->validated());
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.returns.show', $return->id)
            ->with('success', 'Devolução registrada com sucesso.');
    }

    /**
     * Display the specified return.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function show(int $id): InertiaResponse
    {
        return Inertia::render('Commercial/Returns/Show', [
            'return' => $this->repository->findWithRelations($id),
        ]);
    }

    /**
     * Confirm the specified return.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirm(int $id): RedirectResponse
    {
        try {
            $this->service->confirmReturn($id);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.returns.show', $id)
            ->with('success', 'Devolução confirmada com sucesso.');
    }

    /**
     * Cancel the specified return.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(int $id): RedirectResponse
    {
        try {
            $this->service->cancelReturn($id);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.returns.show', $id)
            ->with('success', 'Devolução cancelada.');
    }
}
