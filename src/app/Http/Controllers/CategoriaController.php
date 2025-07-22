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
    public function inicio()
    {
        $categorias = $this->categoriaRepository->getAll();
        return view('categorias.categoria',compact('categorias'));
    }
    public function index()
    {
        $categorias =$this->categoriaRepository->index();
        return view('categorias.index',compact('categorias'));
    }

    public function cadastro()
    {
        return view('categorias.cadastro');
    }

    public function inserirCategoria(Request $request)
    {
        $this->categoriaRepository->inserirCategoria($request);
        return redirect()->route('categoria.index')->with('success', 'Inserido com sucesso');
    }

    public function produto($categoriaId)
    {
        $categoria = Categoria::find($categoriaId)->nome_categoria; 
        $produtos =  Gate::allows('view_post') ? Categoria::find($categoriaId)->produtos()->paginate(2) : Categoria::find($categoriaId)->produtos()->where('status', 1)->paginate(2);
        return view('categorias.produto',compact('categoria','produtos'));

    }

    public function editar($categoriaId)
    {
        $categorias = $this->categoriaRepository->editar($categoriaId);
        return view('categorias.editar', compact('categorias'));
    }

    public function salvarEditar(Request $request,$categoriaId)
    {
        $this->categoriaRepository->salvarEditar($request,$categoriaId);
        return redirect()->route('categoria.index')->with('success', 'Editado com sucesso');
    }

    public function status($statusId)
    {   
        $this->categoriaRepository->editar($statusId);
    }
}
