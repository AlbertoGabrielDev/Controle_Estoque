<?php

namespace App\Repositories;

use App\Models\Permission;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\RoleRepository;

use App\Models\Role;
use App\Validators\RoleValidator;
use Illuminate\Http\Request;

/**
 * Class RoleRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class RoleRepositoryEloquent extends BaseRepository implements RoleRepository
{
  
    public function model()
    {
        return Role::class;
    }

    public function index()
    {
        $roles = Role::all();
        return $roles;
    }

    public function inserirRole(array $data)
    {
        return $this->model->create($data);
    }

    public function cadastro()
    {
        $permissions = Permission::all(); 
        return $permissions;
    }

    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
