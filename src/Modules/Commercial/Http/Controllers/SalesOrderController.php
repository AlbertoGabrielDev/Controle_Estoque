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
use Modules\Commercial\Http\Requests\SalesOrderStoreRequest;
use Modules\Commercial\Http\Requests\SalesOrderConfirmRequest;
use Modules\Commercial\Http\Requests\SalesOrderCancelRequest;
use Modules\Commercial\Http\Requests\SalesOrderUpdateRequest;
use Modules\Commercial\Models\CommercialSalesOrder;
use Modules\Commercial\Repositories\CommercialSalesOrderRepository;
use Modules\Commercial\Services\SalesOrderService;
use RuntimeException;

class SalesOrderController extends Controller
{
    public function __construct(
        private SalesOrderService $service,
        private CommercialSalesOrderRepository $repository,
        private DataTableService $dt,
    ) {
    }

    /**
     * Display a listing of sales orders.
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

        return Inertia::render('Commercial/Orders/Index', ['filters' => $filters]);
    }

    /**
     * Return DataTables JSON for sales orders.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        [$query, $columnsMap] = CommercialSalesOrder::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    $canEdit = $row->c2 === 'rascunho';
                    $showUrl = route('commercial.orders.show', $row->id);
                    $show = sprintf(
                        '<a href="%s" class="p-2 text-blue-600 hover:bg-blue-50 rounded-md inline-flex items-center" title="Ver"><i class="fas fa-eye"></i></a>',
                        e($showUrl)
                    );
                    return DataTableActions::wrap([
                        $show,
                        DataTableActions::edit('commercial.orders.edit', $row->id, $canEdit),
                    ]);
                });
            }
        );
    }

    /**
     * Show the form for creating a new sales order.
     *
     * @return \Inertia\Response
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('Commercial/Orders/Create', $this->repository->formPayload());
    }

    /**
     * Store a newly created sales order.
     *
     * @param \Modules\Commercial\Http\Requests\SalesOrderStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SalesOrderStoreRequest $request): RedirectResponse
    {
        try {
            $order = $this->service->createOrder($request->validated());
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.orders.show', $order->id)
            ->with('success', 'Pedido de venda criado com sucesso.');
    }

    /**
     * Display the specified sales order.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function show(int $id): InertiaResponse
    {
        return Inertia::render('Commercial/Orders/Show', [
            'order' => $this->repository->findWithRelations($id),
        ]);
    }

    /**
     * Show the form for editing the specified sales order.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function edit(int $id): InertiaResponse
    {
        return Inertia::render('Commercial/Orders/Edit', array_merge(
            ['order' => $this->repository->findForEdit($id)],
            $this->repository->formPayload()
        ));
    }

    /**
     * Update the specified sales order.
     *
     * @param \Modules\Commercial\Http\Requests\SalesOrderUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SalesOrderUpdateRequest $request, int $id): RedirectResponse
    {
        try {
            $this->service->updateOrder($id, $request->validated());
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.orders.show', $id)
            ->with('success', 'Pedido atualizado com sucesso.');
    }

    /**
     * Confirm the specified sales order.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirm(SalesOrderConfirmRequest $request, int $id): RedirectResponse
    {
        try {
            $this->service->confirmOrder($id);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.orders.show', $id)
            ->with('success', 'Pedido confirmado com sucesso.');
    }

    /**
     * Cancel the specified sales order.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(SalesOrderCancelRequest $request, int $id): RedirectResponse
    {
        try {
            $this->service->cancelOrder($id);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.orders.show', $id)
            ->with('success', 'Pedido cancelado.');
    }
}
