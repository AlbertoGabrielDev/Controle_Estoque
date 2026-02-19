<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleMenuPermission;
use App\Services\DataTableService;
use App\Support\DataTableActions;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function __construct(
        private DataTableService $dt
    ) {
    }

    public function index(Request $request)
    {
        return Inertia::render('Roles/Index', [
            'filters' => [
                'q' => (string) $request->query('q', ''),
            ],
        ]);
    }

    public function data(Request $request)
    {
        [$query, $columnsMap] = Role::makeDatatableQuery($request);

        return $this->dt->make(
            $query,
            $columnsMap,
            rawColumns: ['acoes'],
            decorate: function ($dt) {
                $dt->addColumn('acoes', function ($row) {
                    return DataTableActions::wrap([
                        DataTableActions::edit('roles.editar', $row->id),
                    ]);
                });
            }
        );
    }

    public function cadastro()
    {
        return Inertia::render('Roles/Create');
    }

    public function buscar(Request $request)
    {
        return redirect()->route('roles.index', [
            'q' => (string) $request->input('name', ''),
        ]);
    }

    public function inserirRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create([
            'name' => $validated['name'],
        ]);

        return redirect()->route('roles.index')->with('success', 'Inserido com sucesso');
    }

    public function editar($id)
    {
        $role = Role::query()->findOrFail($id, ['id', 'name']);
        $statusPermission = Permission::firstOrCreate(['name' => 'status']);

        $permissions = Permission::query()
            ->where('name', '!=', 'status')
            ->orderBy('name')
            ->get(['id', 'name']);

        $menus = Menu::query()
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('order')
            ->get(['id', 'name', 'slug']);

        $currentPermissions = RoleMenuPermission::query()
            ->where('role_id', $role->id)
            ->get(['menu_id', 'permission_id']);

        $selectedPermissions = $currentPermissions
            ->where('permission_id', '!=', $statusPermission->id)
            ->groupBy('menu_id')
            ->map(fn ($group) => $group->pluck('permission_id')->map(fn ($id) => (int) $id)->values())
            ->toArray();

        $statusEnabled = $currentPermissions
            ->contains(fn ($item) => (int) $item->permission_id === (int) $statusPermission->id);

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
            'menus' => $menus,
            'selectedPermissions' => $selectedPermissions,
            'statusEnabled' => $statusEnabled,
        ]);
    }

    public function salvarEditar(Request $request, $roleId)
    {
        $validated = $request->validate([
            'global_permissions.status' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'array',
            'permissions.*.*' => 'integer|exists:permissions,id',
        ]);

        $role = Role::query()->findOrFail($roleId);
        $menus = Menu::query()
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->get(['id']);

        $statusPermission = Permission::firstOrCreate(['name' => 'status']);
        $toggleStatus = (bool) data_get($validated, 'global_permissions.status', false);

        if ($toggleStatus) {
            foreach ($menus as $menu) {
                RoleMenuPermission::updateOrCreate(
                    [
                        'role_id' => $role->id,
                        'menu_id' => $menu->id,
                        'permission_id' => $statusPermission->id,
                    ],
                    []
                );
            }
        } else {
            RoleMenuPermission::query()
                ->where('role_id', $role->id)
                ->where('permission_id', $statusPermission->id)
                ->delete();
        }

        $inputPermissions = data_get($validated, 'permissions', []);
        $currentPermissions = RoleMenuPermission::query()
            ->where('role_id', $role->id)
            ->get();

        $newMap = [];

        foreach ($inputPermissions as $menuId => $permissionIds) {
            foreach ((array) $permissionIds as $permissionId) {
                if ((int) $permissionId === (int) $statusPermission->id) {
                    continue;
                }

                $key = $menuId . '_' . $permissionId;
                $newMap[$key] = true;

                RoleMenuPermission::updateOrCreate(
                    [
                        'role_id' => $role->id,
                        'menu_id' => (int) $menuId,
                        'permission_id' => (int) $permissionId,
                    ],
                    []
                );
            }
        }

        foreach ($currentPermissions as $perm) {
            if ((int) $perm->permission_id === (int) $statusPermission->id) {
                continue;
            }

            $key = $perm->menu_id . '_' . $perm->permission_id;
            if (!isset($newMap[$key])) {
                RoleMenuPermission::query()
                    ->where('role_id', $perm->role_id)
                    ->where('menu_id', $perm->menu_id)
                    ->where('permission_id', $perm->permission_id)
                    ->delete();
            }
        }

        return redirect()->route('roles.editar', $role->id)->with('success', 'Permissoes atualizadas com sucesso!');
    }

    public function updateStatus($model, $id)
    {
        return response()->json([
            'status' => 422,
            'message' => 'Role nao possui status para alternancia.',
            'type' => 'warning',
        ], 422);
    }
}
