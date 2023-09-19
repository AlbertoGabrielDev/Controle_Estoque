<?php
namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Produto;
use App\Models\CategoriaProduto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function inicio()
    {
        $categorias = Categoria::where('status', 1)->get();
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

    public function produto(Request $request , $categoriaId)
    {
        $categoria = Categoria::find($categoriaId);
        $produtos = $categoria->produtos()->get();
        return view('categorias.produto',compact('produtos'));
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
        return redirect()->route('categoria.index');
    }

    public function status(Request $request, $statusId)
    {
        $status = Categoria::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return response()->json(['status' => $status->status]);
    }
}
