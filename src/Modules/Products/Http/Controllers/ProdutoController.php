<?php

namespace Modules\Products\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\Products\Http\Requests\ValidacaoProduto;
use Modules\Products\Http\Requests\ValidacaoProdutoEditar;
use Modules\Products\Services\ProdutoService;

class ProdutoController extends Controller
{
    public function __construct(private ProdutoService $produtoService)
    {
    }

    public function index(Request $request)
    {
        return Inertia::render('Products/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'status' => (string) $request->query('status', ''),
            ],
        ]);
    }

    public function data(Request $request)
    {
        return $this->produtoService->datatable($request);
    }

    public function cadastro()
    {
        return Inertia::render('Products/Create', $this->produtoService->cadastroPayload());
    }

    public function inserirCadastro(ValidacaoProduto $request)
    {
        $this->produtoService->inserir($request->validated(), Auth::id());

        return redirect()->route('produtos.index')->with('success', 'Inserido com sucesso');
    }

    public function buscarProduto(Request $request)
    {
        return redirect()->route('produtos.index', [
            'q' => (string) $request->input('nome_produto', ''),
        ]);
    }

    public function editar($produtoId)
    {
        return Inertia::render('Products/Edit', $this->produtoService->editarPayload((int) $produtoId));
    }

    public function salvarEditar(ValidacaoProdutoEditar $request, $produtoId)
    {
        $this->produtoService->salvarEdicao((int) $produtoId, $request->validated());

        return redirect()->route('produtos.index')->with('success', 'Editado com sucesso');
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        return response()->json($this->produtoService->search($q));
    }
}
