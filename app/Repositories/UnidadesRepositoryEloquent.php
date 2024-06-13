<?php

namespace App\Repositories;

use App\Models\Unidades;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UnidadesRepository;
use App\Validators\UnidadesValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
/**
 * Class UnidadesRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UnidadesRepositoryEloquent extends BaseRepository implements UnidadesRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Unidades::class;
    }

    public function index()
    {
        $unidades = Gate::allows('permissao') ?  $marcas = Unidades::paginate(15) : $marcas = Unidades::where('status', 1)->paginate(15);
        return $unidades;
    }

    public function inserirUnidade(Request $request)
    {
      
        Unidades::create([
            'nome' => $request->nome,
            'id_users_fk' => Auth::id(),
        ]);
       
    }
    public function editar($unidadeId)
    {
        $unidades = Unidades::where('id_unidade', $unidadeId)->get();
        return $unidades;
    }

    public function salvarEditar(Request $request, $unidadeId)
    {
        dd($request);
        $unidades = Unidades::where('id_unidade', $unidadeId)
        ->update([
            'nome' => $request->nome
        ]);
        return $unidades;
    }
    public function status($statusId)
    {   
        $status = Unidades::findOrFail($statusId);
        $status->status = ($status->status == 1) ? 0 : 1;
        $status->save();
        return $status;
    }

    public function buscar(Request $request) 
    {
        if (Gate::allows('permissao')) {
            $unidades = Unidades::where('nome', 'like', '%' . $request->input('nome') . '%')->paginate(15);
        } else {
            $unidades = Unidades::where('nome', 'like', '%' . $request->input('nome') . '%')->where('status',1)->paginate(15);
        }
        
        return $unidades;
    } 

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
