<?php

namespace App\Repositories;

use App\Http\Requests\ValidacaoMarca;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\marcaRepository;
use Illuminate\Support\Facades\Gate;
use App\Models\Marca;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/**
 * Class MarcaRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class MarcaRepositoryEloquent extends BaseRepository implements marcaRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Marca::class;
    }

    public function index(){
        $marcas = Gate::allows('permissao') ? $marcas = Marca::paginate(15) : $marcas = Marca::where('status', 1)->paginate(15);
        return $marcas;
    }

    public function cadastro( ValidacaoMarca $request ){
        Marca::create([
            'nome_marca' => $request->nome_marca,
            'status' => $request->status,
            'id_users_fk' => Auth::id()
        ]);
      
    }

    public function buscar(Request $request){
     
        if (Gate::allows('permissao')) {
            $marcas = Marca::where('nome_marca', 'like', '%' . $request->input('nome_marca') . '%')->paginate(15);
        } else {
            $marcas = Marca::where('nome_marca', 'like', '%' . $request->input('nome_marca') . '%')->where('status',1)->paginate(15);
        }
        return $marcas;
    }

    public function editar(ValidacaoMarca $request, $marcaId){
       $paginaEditar = Marca::where('id_marca' , $marcaId)
            ->update([
            'nome_marca' => $request->nome_marca, 
            'status' => $request->status
            ]); 
            return $paginaEditar;
    }

    public function status($statusId){
        $status = Marca::findOrFail($statusId);
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
