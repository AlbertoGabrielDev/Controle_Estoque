<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Repositories\RoleRepository;
use App\Repositories\RoleRepositoryEloquent;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roleRepository;
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

   public function index(){
    $roles = $this->roleRepository->index();

    return view('role.index', compact('roles'));
   }

   public function cadastro()
    {
        $cadastro = $this->roleRepository->cadastro();
        return view('role.cadastro',$cadastro);
    }

    public function inserirRole(Request $request)
    { 
        $data = $request->all();
        $post = $this->roleRepository->create($data);
        return redirect()->route('roles.index')->with('success', 'Inserido com sucesso');
    }

    public function edit(Role $role)
{
    $permissions = Permission::all();
    return view('roles.edit', compact('role', 'permissions'));
}

public function update(Request $request, Role $role)
{
    $role->permissions()->sync($request->permissions);
    return redirect()->route('roles.index');
}
}
