<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CategoriaRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\Categoria;
use App\Validators\CategoriaValidator;
use Illuminate\Http\Request;

/**
 * Class CategoriaRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CategoriaRepositoryEloquent extends BaseRepository implements CategoriaRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Categoria::class;
    }

    public function getAll()
    {
        $categorias = Gate::allows('permissao') ? Categoria::get() : Categoria::where('status', 1)->get();
        return $categorias;
       }
    public function index()
    {
        $categorias = Categoria::all();
        return $categorias;
    }

    public function inserirCategoria(Request $request)
    {
        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            $requestImage = $request->imagem;
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")). "." . $extension;
            $requestImage->move(public_path('img/categorias'), $imageName);

            Categoria::create([
                'nome_categoria' => $request->categoria,
                'id_users_fk' => Auth::id(),
                'imagem' => $imageName
            ]);
        
        };
       
    }
    public function editar($categoriaId)
    {
        $categorias = Categoria::where('id_categoria', $categoriaId)->get();
        return $categorias;
    }

    public function salvarEditar(Request $request, $categoriaId)
    {
        Categoria::where('id_categoria', $categoriaId)
            ->update([
                'nome_categoria' => $request->nome_categoria,
                'id_users_fk' => $request->id_users_fk
            ]);
    
    }
    public function status($statusId)
    {   
        $status = Categoria::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return response()->json(['status' => $status->status]);
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
