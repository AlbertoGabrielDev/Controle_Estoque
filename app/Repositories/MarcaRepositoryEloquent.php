<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\MarcaRepository;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ValidacaoMarca;

/**
 * Class MarcaRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class MarcaRepositoryEloquent extends BaseRepository implements MarcaRepository
{
    protected $fieldSearchable = [
        'nome_marca' => 'like',
    ];

    public function model()
    {
        return Marca::class;
    }

    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }
    
    public function index()
    {
        if (Gate::allows('permissao')) {
            $query = Marca::query();
            $this->applyLikeConditions($query, request()->get('searchLike'));
            return $query->paginate(15);
        }

        return Marca::where('status', 1)->paginate(15);
    }

    public function cadastro()
    {   
        return view('marca.cadastro');
    }

    public function editar($marcaId)
    {
        $marcas = Marca::where('id_marca' , $marcaId)->get();
        return compact('marcas');
    }

    public function salvarEditar(ValidacaoMarca $request, $marcaId)
    {
        Marca::where('id_marca' , $marcaId)
        ->update([
           'nome_marca' => $request->nome_marca 
        ]);
    }

    public function inserirMarca(ValidacaoMarca $request)
    {
        Marca::create([
            'nome_marca' => $request->nome_marca,
            'id_users_fk' => Auth::id()
        ]);
        
    }

    public function status($statusId)
    {
        $status = Marca::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return response()->json(['status' => $status->status]);
    }

}
