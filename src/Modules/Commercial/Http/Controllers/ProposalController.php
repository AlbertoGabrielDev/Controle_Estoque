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
use Modules\Commercial\Http\Requests\ProposalStoreRequest;
use Modules\Commercial\Http\Requests\ProposalSendRequest;
use Modules\Commercial\Http\Requests\ProposalApproveRequest;
use Modules\Commercial\Http\Requests\ProposalRejectRequest;
use Modules\Commercial\Http\Requests\ProposalUpdateRequest;
use Modules\Commercial\Models\CommercialProposal;
use Modules\Commercial\Repositories\CommercialProposalRepository;
use Modules\Commercial\Services\ProposalService;
use Modules\Commercial\Services\SalesOrderService;
use RuntimeException;

class ProposalController extends Controller
{
    public function __construct(
        private ProposalService $service,
        private SalesOrderService $orderService,
        private CommercialProposalRepository $repository,
        private DataTableService $dt,
    ) {
    }

    /**
     * Display a listing of proposals.
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

        return Inertia::render('Commercial/Proposals/Index', ['filters' => $filters]);
    }

    /**
     * Return DataTables JSON for proposals.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        [$query, $columnsMap] = CommercialProposal::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    $canEdit = $row->c2 === 'rascunho';
                    $showUrl = route('commercial.proposals.show', $row->id);
                    $show = sprintf(
                        '<a href="%s" class="p-2 text-blue-600 hover:bg-blue-50 rounded-md inline-flex items-center" title="Ver"><i class="fas fa-eye"></i></a>',
                        e($showUrl)
                    );
                    return DataTableActions::wrap([
                        $show,
                        DataTableActions::edit('commercial.proposals.edit', $row->id, $canEdit),
                    ]);
                });
            }
        );
    }

    /**
     * Show the form for creating a new proposal.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function create(Request $request): InertiaResponse
    {
        $payload = $this->repository->formPayload();

        if ($request->query('opportunity_id')) {
            $payload['opportunity_id'] = (int) $request->query('opportunity_id');
        }

        return Inertia::render('Commercial/Proposals/Create', $payload);
    }

    /**
     * Store a newly created proposal.
     *
     * @param \Modules\Commercial\Http\Requests\ProposalStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProposalStoreRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            $proposal = isset($data['opportunity_id'])
                ? $this->service->createFromOpportunity($data['opportunity_id'], $data)
                : $this->service->createProposal($data);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.proposals.show', $proposal->id)
            ->with('success', 'Proposta criada com sucesso.');
    }

    /**
     * Display the specified proposal.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function show(int $id): InertiaResponse
    {
        return Inertia::render('Commercial/Proposals/Show', [
            'proposal' => $this->repository->findWithRelations($id),
        ]);
    }

    /**
     * Show the form for editing the specified proposal.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function edit(int $id): InertiaResponse
    {
        return Inertia::render('Commercial/Proposals/Edit', array_merge(
            ['proposal' => $this->repository->findForEdit($id)],
            $this->repository->formPayload()
        ));
    }

    /**
     * Update the specified proposal.
     *
     * @param \Modules\Commercial\Http\Requests\ProposalUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProposalUpdateRequest $request, int $id): RedirectResponse
    {
        try {
            $this->service->updateProposal($id, $request->validated());
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.proposals.show', $id)
            ->with('success', 'Proposta atualizada com sucesso.');
    }

    /**
     * Send the proposal to the customer.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(ProposalSendRequest $request, int $id): RedirectResponse
    {
        try {
            $this->service->sendProposal($id);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.proposals.show', $id)
            ->with('success', 'Proposta enviada com sucesso.');
    }

    /**
     * Approve the proposal.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(ProposalApproveRequest $request, int $id): RedirectResponse
    {
        try {
            $this->service->approveProposal($id);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.proposals.show', $id)
            ->with('success', 'Proposta aprovada com sucesso.');
    }

    /**
     * Reject the proposal.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(ProposalRejectRequest $request, int $id): RedirectResponse
    {
        try {
            $this->service->rejectProposal($id);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.proposals.show', $id)
            ->with('success', 'Proposta rejeitada.');
    }

    /**
     * Convert an approved proposal to a sales order.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function convertToOrder(int $id): RedirectResponse
    {
        try {
            $order = $this->service->convertToSalesOrder($id, $this->orderService);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.orders.show', $order->id)
            ->with('success', 'Proposta convertida em pedido de venda.');
    }
}
