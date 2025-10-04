<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerSegmentRequest;
use App\Models\CustomerSegment;
use App\Services\DataTableService;
use Blade;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerSegmentController extends Controller
{

    public function __construct(
        private DataTableService $dt,
    ) {
    }
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $segmentos = CustomerSegment::query()
            ->when($q, fn($qry) => $qry->where('nome', 'like', "%{$q}%"))
            ->orderBy('nome')
          
           ;

        return Inertia::render('Segments/Index', [
            'segmentos' => $segmentos,
            'q' => $q,
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
                    $editBtn = Blade::render(
                        '<x-edit-button :route="$route" :model-id="$id" />',
                        ['route' => 'segmentos.edit', 'id' => $row->id]
                    );
                  
                    $html = trim($editBtn);
                    if ($html === '') {
                        $html = '<span class="inline-block w-8 h-8 opacity-0" aria-hidden="true">&nbsp;</span>';
                    }
                    return '<div class="flex gap-2 justify-start items-center">' . $html . '</div>';
                });
            }
        );
    }

    public function create()
    {
        return Inertia::render('Segmentos/Create');
    }

    public function store(CustomerSegmentRequest $request)
    {
        CustomerSegment::create($request->validated());
        return redirect()->route('segmentos.index')->with('success', 'Segmento criado.');
    }

    public function edit(CustomerSegment $segment)
    {
        return Inertia::render('Segmentos/Edit', ['segmento' => $segment]);
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
