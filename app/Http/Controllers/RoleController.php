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

        $permissions = Permission::where('name', '!=', 'status')->get();
        $menus = Menu::with('roles')->whereNotNull('slug')->where('slug', '!=', '')->get();

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
        $menus = Menu::whereNotNull('slug')->where('slug', '!=', '')->get();
    
        $toggleStatus = $request->input('global_permissions.status', '0') === '1';
        $permissionStatus = Permission::firstOrCreate(['name' => 'status']);
    
        if ($toggleStatus) {
            foreach ($menus as $menu) {
                RoleMenuPermission::updateOrCreate(
                    [
                        'role_id' => $role->id,
                        'menu_id' => $menu->id,
                        'permission_id' => $permissionStatus->id
                    ],
                    []
                );
            }
        } else {
            RoleMenuPermission::where('role_id', $role->id)
                ->where('permission_id', $permissionStatus->id)
                ->delete();
        }
    
        // Lógica para demais permissões
        $inputPermissions = $request->input('permissions', []);
        $currentPermissions = RoleMenuPermission::where('role_id', $role->id)->get();
    
        // Mapas para comparação
        $currentMap = $currentPermissions->map(function ($item) {
            return "{$item->menu_id}_{$item->permission_id}";
        })->toArray();
    
        $newMap = [];
    
        foreach ($inputPermissions as $menuId => $permissionIds) {
            foreach ($permissionIds as $permissionId) {
                // Ignorar "status" que já está sendo tratado separadamente
                if ($permissionId == $permissionStatus->id) continue;
    
                $key = "{$menuId}_{$permissionId}";
                $newMap[] = $key;
    
                // Criar se não existir
                if (!in_array($key, $currentMap)) {
                    RoleMenuPermission::create([
                        'role_id' => $role->id,
                        'menu_id' => $menuId,
                        'permission_id' => $permissionId
                    ]);
                }
            }
        }
    
        // Remover permissões que não estão mais no formulário (exceto "status")
        foreach ($currentPermissions as $perm) {
            $key = "{$perm->menu_id}_{$perm->permission_id}";
            if (!in_array($key, $newMap) && $perm->permission_id != $permissionStatus->id) {
                $perm->delete();
            }
        }
    
        return redirect()->back()->with('success', 'Permissões atualizadas com sucesso!');
    }
    
}
