<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerSegmentRequest;
use App\Models\CustomerSegment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerSegmentController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $segmentos = CustomerSegment::query()
            ->when($q, fn($qry)=> $qry->where('nome','like',"%{$q}%"))
            ->orderBy('nome')
            ->paginate(10)
            ->appends($request->all());
        
        return Inertia::render('Segmentos/Index', [
            'segmentos' => $segmentos,
            'q' => $q,
        ]);
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
