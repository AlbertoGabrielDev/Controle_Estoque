<?php

namespace App\Repositories;

use App\Http\Requests\ValidacaoUsuario;
use App\Models\User;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UsuarioRepository;
use App\Validators\UsuarioValidator;
use Illuminate\Http\Request;

/**
 * Class UsuarioRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UsuarioRepositoryEloquent extends BaseRepository implements UsuarioRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    public function Index(){
        $usuarios = User::where('id', '!=', 1)->paginate(2);
        return $usuarios;
    }

    public function inserir(Request $request){
        User::create([
            'name' => $request->name,
            'email' =>$request->email,
            'password'  => $request->password,
            'id_unidade_fk' =>$request->id_unidade_fk     
       ]);
    }

    public function buscar(Request $request)
    {   
        $usuarios = User::where('name', 'like' , '%' . $request->input('name'). '%')
        ->where('id' , '!=' , 1 )
        ->paginate(2);
        return $usuarios;
    }

    public function salvarEditar(ValidacaoUsuario $request, $userId)
    {   
        $users = User::where('id',$userId)
        ->update([
            'name'  =>$request->name,
            'email' =>$request->email
        ]);
      
        return $users;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
