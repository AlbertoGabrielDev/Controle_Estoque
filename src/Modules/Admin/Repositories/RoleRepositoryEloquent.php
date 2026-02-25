<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Permission;
use Modules\Admin\Models\Role;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class RoleRepositoryEloquent.
 *
 * @package namespace Modules\Admin\Repositories;
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
