<?php

namespace Modules\Customers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Customers\Http\Requests\CustomerSegmentRequest;
use Modules\Customers\Models\CustomerSegment;
use Modules\Customers\Services\CustomerSegmentService;

class CustomerSegmentController extends Controller
{
    public function __construct(
        private DataTableService $dt,
        private CustomerSegmentService $service,
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('Segments/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
            ],
        ]);
    }

    public function data(Request $request)
    {
        [$query, $columnsMap] = CustomerSegment::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('segmentos.edit', $row->id),
                    ]);
                });
            }
        );
    }

    public function create()
    {
        return Inertia::render('Segments/Create');
    }

    public function store(CustomerSegmentRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('segmentos.index')->with('success', 'Segmento criado.');
    }

    public function edit(CustomerSegment $segment)
    {
        return Inertia::render('Segments/Edit', [
            'segmento' => $this->service->findOrFail($segment->id),
        ]);
    }

    public function update(CustomerSegmentRequest $request, CustomerSegment $segment)
    {
        $this->service->update($segment->id, $request->validated());

        return redirect()->route('segmentos.index')->with('success', 'Segmento atualizado.');
    }

    public function destroy(CustomerSegment $segment)
    {
        $this->service->delete($segment->id);

        return redirect()->route('segmentos.index')->with('success', 'Segmento excluido.');
    }
}
