<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleMenuPermission;
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

    public function index()
    {
        $roles = $this->roleRepository->index();

        return view('role.index', compact('roles'));
    }

    public function cadastro()
    {
        $cadastro = $this->roleRepository->cadastro();
        return view('role.cadastro', $cadastro);
    }

    public function inserirRole(Request $request)
    {
        $data = $request->all();
        $post = $this->roleRepository->create($data);
        return redirect()->route('roles.index')->with('success', 'Inserido com sucesso');
    }

    public function editar($id)
    {
        // Corrigir o nome da relação para roleMenuPermissions
        $role = Role::with(['roleMenuPermissions' => function ($query) {
            $query->select('menu_id', 'permission_id');
        }])->findOrFail($id);
    
        $permissions = Permission::all();
        $menus = Menu::with('roles')->whereNotNull('slug')->where('slug', '!=', '') ->get();
    
        // Acessar através da relação correta
        $rolePermissions = $role->roleMenuPermissions->mapWithKeys(function ($item) {
            return ["{$item->menu_id}-{$item->permission_id}" => true];
        });
    
        return view('role.editar', [
            'role' => $role,
            'permissions' => $permissions,
            'menus' => $menus,
            'rolePermissions' => $rolePermissions
        ]);
    }

    public function salvarEditar(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        
        // Limpar permissões existentes
        RoleMenuPermission::where('role_id', $role->id)->delete();

        // Adicionar novas permissões
        $permissions = $request->input('permissions', []);
        
        foreach ($permissions as $menuId => $permissionIds) {
            foreach ($permissionIds as $permissionId) {
                RoleMenuPermission::create([
                    'role_id' => $role->id,
                    'menu_id' => $menuId,
                    'permission_id' => $permissionId
                ]);
            }
        }

        return redirect()->back()->with('success', 'Permissões atualizadas com sucesso!');
    }
}
