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
use Modules\Commercial\Http\Requests\OpportunityStoreRequest;
use Modules\Commercial\Http\Requests\OpportunityStatusRequest;
use Modules\Commercial\Http\Requests\OpportunityUpdateRequest;
use Modules\Commercial\Models\CommercialOpportunity;
use Modules\Commercial\Repositories\CommercialOpportunityRepository;
use Modules\Commercial\Services\OpportunityService;
use RuntimeException;

class OpportunityController extends Controller
{
    public function __construct(
        private OpportunityService $service,
        private CommercialOpportunityRepository $repository,
        private DataTableService $dt,
    ) {
    }

    /**
     * Display a listing of opportunities.
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

        return Inertia::render('Commercial/Opportunities/Index', ['filters' => $filters]);
    }

    /**
     * Return DataTables JSON for opportunities.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        [$query, $columnsMap] = CommercialOpportunity::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    $showUrl = route('commercial.opportunities.show', $row->id);
                    $show = sprintf(
                        '<a href="%s" class="p-2 text-blue-600 hover:bg-blue-50 rounded-md inline-flex items-center" title="Ver"><i class="fas fa-eye"></i></a>',
                        e($showUrl)
                    );
                    return DataTableActions::wrap([
                        $show,
                        DataTableActions::edit('commercial.opportunities.edit', $row->id),
                    ]);
                });
            }
        );
    }

    /**
     * Show the form for creating a new opportunity.
     *
     * @return \Inertia\Response
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('Commercial/Opportunities/Create', $this->repository->formPayload());
    }

    /**
     * Store a newly created opportunity.
     *
     * @param \Modules\Commercial\Http\Requests\OpportunityStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(OpportunityStoreRequest $request): RedirectResponse
    {
        try {
            $opportunity = $this->service->createOpportunity($request->validated());
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.opportunities.show', $opportunity->id)
            ->with('success', 'Oportunidade criada com sucesso.');
    }

    /**
     * Display the specified opportunity.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function show(int $id): InertiaResponse
    {
        return Inertia::render('Commercial/Opportunities/Show', [
            'opportunity' => $this->repository->findWithRelations($id),
        ]);
    }

    /**
     * Show the form for editing the specified opportunity.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function edit(int $id): InertiaResponse
    {
        return Inertia::render('Commercial/Opportunities/Edit', array_merge(
            ['opportunity' => $this->repository->findForEdit($id)],
            $this->repository->formPayload()
        ));
    }

    /**
     * Update the specified opportunity.
     *
     * @param \Modules\Commercial\Http\Requests\OpportunityUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(OpportunityUpdateRequest $request, int $id): RedirectResponse
    {
        try {
            $this->service->updateOpportunity($id, $request->validated());
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.opportunities.show', $id)
            ->with('success', 'Oportunidade atualizada com sucesso.');
    }

    /**
     * Mark opportunity as converting and redirect to proposal creation.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function convertToProposal(int $id): RedirectResponse
    {
        try {
            $this->service->convertToProposal($id);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.proposals.create', ['opportunity_id' => $id])
            ->with('success', 'Crie a proposta para esta oportunidade.');
    }

    /**
     * Change the status of the specified opportunity.
     *
     * @param \Modules\Commercial\Http\Requests\OpportunityStatusRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeStatus(OpportunityStatusRequest $request, int $id): RedirectResponse
    {
        try {
            $data = $request->validated();

            if ($data['status'] === 'perdido') {
                $this->service->markAsLost($id, $data['motivo_perda'] ?? '');
            } elseif ($data['status'] === 'ganho') {
                $this->service->markAsWon($id);
            } else {
                $this->service->updateStatus($id, $data['status']);
            }
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.opportunities.show', $id)
            ->with('success', 'Status da oportunidade atualizado.');
    }
}
