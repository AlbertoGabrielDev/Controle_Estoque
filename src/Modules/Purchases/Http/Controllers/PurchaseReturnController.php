<?php

namespace Modules\Purchases\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Modules\Purchases\Http\Requests\PurchaseReturnStoreRequest;
use Modules\Purchases\Models\PurchaseReturn;
use Modules\Purchases\Services\PurchaseReturnService;
use RuntimeException;

class PurchaseReturnController extends Controller
{
    public function __construct(private PurchaseReturnService $service)
    {
    }

    /**
     * Display a listing of purchase returns.
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

        $query = PurchaseReturn::query()->with('items');

        if ($filters['q'] !== '') {
            $query->where('numero', 'like', '%' . $filters['q'] . '%');
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if ($filters['data_inicio'] !== '') {
            $query->whereDate('data_devolucao', '>=', $filters['data_inicio']);
        }

        if ($filters['data_fim'] !== '') {
            $query->whereDate('data_devolucao', '<=', $filters['data_fim']);
        }

        $returns = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return Inertia::render('Purchases/Returns/Index', [
            'filters' => $filters,
            'returns' => $returns,
        ]);
    }

    /**
     * Show the form for creating a new return.
     *
     * @return \Inertia\Response
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('Purchases/Returns/Create');
    }

    /**
     * Store a newly created return.
     *
     * @param \Modules\Purchases\Http\Requests\PurchaseReturnStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PurchaseReturnStoreRequest $request): RedirectResponse
    {
        try {
            $return = $this->service->createReturn($request->validated());
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.returns.show', $return->id)
            ->with('success', 'Devolucao criada com sucesso.');
    }

    /**
     * Display the specified return.
     *
     * @param int $returnId
     * @return \Inertia\Response
     */
    public function show(int $returnId): InertiaResponse
    {
        $return = PurchaseReturn::query()
            ->with(['items', 'order', 'receipt'])
            ->findOrFail($returnId);

        return Inertia::render('Purchases/Returns/Show', [
            'purchaseReturn' => $return,
        ]);
    }

    /**
     * Confirm the specified return.
     *
     * @param int $returnId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirm(int $returnId): RedirectResponse
    {
        try {
            $this->service->confirmReturn($returnId);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.returns.show', $returnId)
            ->with('success', 'Devolucao confirmada com sucesso.');
    }

    /**
     * Cancel the specified return.
     *
     * @param int $returnId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(int $returnId): RedirectResponse
    {
        try {
            $this->service->cancelReturn($returnId);
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->route('purchases.returns.show', $returnId)
            ->with('success', 'Devolucao cancelada com sucesso.');
    }
}
