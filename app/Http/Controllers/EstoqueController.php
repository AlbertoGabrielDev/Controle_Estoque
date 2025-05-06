<?php
namespace App\Http\Controllers;
use App\Models\Estoque;
use App\Models\Marca;
use App\Models\Produto;
use App\Models\Fornecedor;
use App\Models\Categoria;
use App\Models\MarcaProduto;
use App\Models\Historico;
use App\Models\CategoriaProduto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ValidacaoEstoque;
use App\Repositories\EstoqueRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
// use Illuminate\Support\Facades\Request;

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

    public function status($statusId)
    {
        if(!auth()->user()->canToggleStatus()) {
            abort(403, 'Sem permissão para esta ação');
        }
        
        $status = Estoque::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        
        return response()->json([
            'status' => $status->status,
            'message' => 'Status atualizado com sucesso!',
            'type' => 'success'
        ]);
    }

}
