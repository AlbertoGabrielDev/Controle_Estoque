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
use Illuminate\Http\Request as Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

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
        return response()->json($estoques);
    }

    public function historico()
    { 
        $historicos = $this->estoqueRepository->historico();
        return response()->json($historicos);
    }

    // public function cadastro()
    // {
    //     $cadastro = $this->estoqueRepository->cadastro();
    //     return response()->json($cadastro);
    // }

    public function buscar(Request $request)
    {
        $buscar = $this->estoqueRepository->buscar($request);
        return response()->json($buscar);

    }

    public function inserirEstoque(ValidacaoEstoque $request)
    { 
        $inserir = $this->estoqueRepository->inserirEstoque($request);
        return response()->json($inserir);
    }

    public function editar($estoqueId)
    {
        $editar = $this->estoqueRepository->editar($estoqueId);
        return response()->json($editar);
    }

    public function salvarEditar(ValidacaoEstoque $request, $estoqueId)
    {
       $salvarEditar = $this->estoqueRepository->salvarEditar($request,$estoqueId);
       return response()->json($salvarEditar);
    }

    public function status($statusId)
    {
      $status = $this->estoqueRepository->status($statusId);
      return response()->json(['status' => $status->status]);
    }

    public function atualizarEstoque(Requests $request,$estoqueId, $operacao)
    {
        $atualizar =  $this->estoqueRepository->atualizarEstoque($request, $estoqueId , $operacao);
        return response()->json($atualizar);

    }

    public function graficoFiltro(Requests $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $quantidade = $this->estoqueRepository->graficoFiltro($startDate, $endDate);
        $totalSum = $quantidade['values']->sum();
       // dd($totalSum);
        return response()->json([
            'labels' => $quantidade['labels'],
            'values' => $quantidade['values'],
            'total_sum' => $totalSum,
        ]);
    }
}
