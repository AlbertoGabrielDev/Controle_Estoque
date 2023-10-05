<?php
namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Produto;
use App\Models\CategoriaProduto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
class CategoriaController extends Controller
{
    public function inicio()
    {
        if (Gate::allows('permissao')) {
            $categorias = Categoria::all();
        } else {
            $categorias = Categoria::where('status', 1)->get();
        }
        return view('categorias.categoria',compact('categorias'));
    }
    public function index()
    {
        $categorias = Categoria::all();
        return view('categorias.index',compact('categorias'));
    }

    public function cadastro()
    {
        return view('categorias.cadastro');
    }

    public function inserirCategoria(Request $request)
    {
        if($request->hasFile('imagem') && $request->file('imagem')->isValid()){
            $requestImage = $request->imagem;
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")). "." . $extension;
            $requestImage->move(public_path('img/categorias'), $imageName);
            $categoria =  Categoria::create([
                'nome_categoria' => $request->categoria,
                'id_users_fk' => Auth::id(),
                'imagem' => $imageName
            ]);
        };
        return redirect()->route('categoria.index')->with('success', 'Inserido com sucesso');
    }

    public function produto($categoriaId)
    {
        $categoria = Categoria::find($categoriaId)->nome_categoria; 
        $produtos = Categoria::find($categoriaId)->produtos()->get();
        return view('categorias.produto',compact('produtos','categoria'));
    }

    public function editar(Request $request, $categoriaId)
    {
        $categorias = Categoria::where('id_categoria', $categoriaId)->get();
        return view('categorias.editar', compact('categorias'));
    }

    public function salvarEditar(Request $request, $categoriaId)
    {
        $categorias = Categoria::where('id_categoria', $categoriaId)
        ->update([
            'nome_categoria' => $request->nome_categoria
        ]);
        return redirect()->route('categoria.index')->with('success', 'Editado com sucesso');
    }

    public function status($statusId)
    {   
        $status = Categoria::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return response()->json(['status' => $status->status]);
    }
}
