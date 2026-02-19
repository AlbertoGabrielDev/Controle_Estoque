<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerSegmentRequest;
use App\Models\CustomerSegment;
use App\Services\DataTableService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Support\DataTableActions;

class CustomerSegmentController extends Controller
{

    public function __construct(
        private DataTableService $dt,
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
        CustomerSegment::create($request->validated());
        return redirect()->route('segmentos.index')->with('success', 'Segmento criado.');
    }

    public function edit(CustomerSegment $segment)
    {
        return Inertia::render('Segments/Edit', ['segmento' => $segment]);
    }

    public function update(CustomerSegmentRequest $request, CustomerSegment $segment)
    {
        $segment->update($request->validated());
        return redirect()->route('segmentos.index')->with('success', 'Segmento atualizado.');
    }

    public function destroy(CustomerSegment $segment)
    {
        $segment->delete();
        return redirect()->route('segmentos.index')->with('success', 'Segmento excluído.');
    }
}
