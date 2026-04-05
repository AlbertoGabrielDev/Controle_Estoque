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
use Modules\Commercial\Http\Requests\DiscountPolicyStoreRequest;
use Modules\Commercial\Http\Requests\DiscountPolicyUpdateRequest;
use Modules\Commercial\Models\CommercialDiscountPolicy;

class DiscountPolicyController extends Controller
{
    public function __construct(private DataTableService $dt)
    {
    }

    /**
     * Display a listing of discount policies.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request): InertiaResponse
    {
        $filters = [
            'q'    => (string) $request->query('q', ''),
            'tipo' => (string) $request->query('tipo', ''),
        ];

        return Inertia::render('Commercial/DiscountPolicies/Index', ['filters' => $filters]);
    }

    /**
     * Return DataTables JSON for discount policies.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        [$query, $columnsMap] = CommercialDiscountPolicy::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('commercial.discount-policies.edit', $row->id),
                    ]);
                });
            }
        );
    }

    /**
     * Show the form for creating a new discount policy.
     *
     * @return \Inertia\Response
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('Commercial/DiscountPolicies/Create');
    }

    /**
     * Display the specified discount policy.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function show(int $id): InertiaResponse
    {
        return Inertia::render('Commercial/DiscountPolicies/Edit', [
            'policy' => CommercialDiscountPolicy::query()->findOrFail($id),
        ]);
    }

    /**
     * Store a newly created discount policy.
     *
     * @param \Modules\Commercial\Http\Requests\DiscountPolicyStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(DiscountPolicyStoreRequest $request): RedirectResponse
    {
        CommercialDiscountPolicy::query()->create($request->validated());

        return redirect()->route('commercial.discount-policies.index')
            ->with('success', 'Política de desconto criada com sucesso.');
    }

    /**
     * Show the form for editing the specified discount policy.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function edit(int $id): InertiaResponse
    {
        return Inertia::render('Commercial/DiscountPolicies/Edit', [
            'policy' => CommercialDiscountPolicy::query()->findOrFail($id),
        ]);
    }

    /**
     * Update the specified discount policy.
     *
     * @param \Modules\Commercial\Http\Requests\DiscountPolicyUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(DiscountPolicyUpdateRequest $request, int $id): RedirectResponse
    {
        CommercialDiscountPolicy::query()->findOrFail($id)->update($request->validated());

        return redirect()->route('commercial.discount-policies.index')
            ->with('success', 'Política de desconto atualizada com sucesso.');
    }
}
