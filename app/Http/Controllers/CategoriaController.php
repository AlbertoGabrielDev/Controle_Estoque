<?php
namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Produto;
use App\Models\CategoriaProduto;
use App\Repositories\CategoriaRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoriaController extends Controller
{

    protected $categoriaRepository;
    public function __construct(CategoriaRepository $categoriaRepository)
    {
        $this->categoriaRepository = $categoriaRepository;
    }
    // public function inicio()
    // {
    //     $categorias = $this->categoriaRepository->getAll();
    //     return response()->json($categorias);
    // }
    public function index()
    {
        $categorias =$this->categoriaRepository->index();
        return response()->json($categorias);
    }

    // public function cadastro()
    // {
    //     return view('categorias.cadastro');
    // }

    public function inserirCategoria(Request $request)
    {
        $inserir = $this->categoriaRepository->inserirCategoria($request);
        return response()->json([$inserir, 'message' => 'Inserido com sucesso'], 200);
    }

    public function produto($categoriaId)
    {
        $categoria = Categoria::find($categoriaId)->nome_categoria; 
        $produto = Gate::allows('permissao') ? Categoria::find($categoriaId)->produtos()->paginate(2) : Categoria::find($categoriaId)->produtos()->where('status', 1)->paginate(2);
        return response()->json([$categoria,$produto ,'message' => 'Inserido com sucesso'], 200);
    }

    public function editar($categoriaId)
    {
        $categoria = $this->categoriaRepository->editar($categoriaId);
        return response()->json([$categoria ,'message' => 'Editado com sucesso'], 200);
    }

    public function salvarEditar(Request $request,$categoriaId)
    {
        $editar = $this->categoriaRepository->salvarEditar($request,$categoriaId);
        return response()->json([$editar ,'message' => 'Editado com sucesso'], 200);
    }

    public function status($statusId)
    {   
        $this->categoriaRepository->editar($statusId);
    }
}
