<?php
namespace App\Http\Controllers;

use App\Http\Requests\ValidacaoEstoque;
use App\Repositories\EstoqueRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstoqueController extends Controller
{    
    protected $estoqueRepository;
    public function __construct(EstoqueRepository $estoqueRepository)
    {
        $this->estoqueRepository = $estoqueRepository;
    }
    public function index()
    {
        $estoques = $this->estoqueRepository->index();
        return view('estoque.index', $estoques);
    }

    public function historico()
    { 
        $historicos = $this->estoqueRepository->historico();
        return view('estoque.historico', compact('historicos'));
    }

    public function cadastro()
    {
        $cadastro = $this->estoqueRepository->cadastro();
        return view('estoque.cadastro',$cadastro);
    }

    public function buscar(Request $request)
    {
        $buscar = $this->estoqueRepository->buscar($request);
        return view('estoque.index',$buscar);
    }

    public function inserirEstoque(ValidacaoEstoque $request)
    {
        $data = $request->merge(['id_users_fk' => Auth::id()])->all();
        $this->estoqueRepository->inserirEstoque($data);
        return redirect()->route('estoque.index')->with('success', 'Inserido com sucesso');
    }

    public function editar($estoqueId)
    {
        $editar = $this->estoqueRepository->editar($estoqueId);
        return view('estoque.editar', $editar);
    }

    public function salvarEditar(ValidacaoEstoque $request, $estoqueId)
    {
        $this->estoqueRepository->salvarEditar($request,$estoqueId);
        return redirect()->route('estoque.index')->with('success', 'Editado com sucesso');
    }

}
