<?php

namespace Modules\Admin\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Modules\Admin\Models\Menu;
use Modules\Admin\Models\Permission;
use Modules\Admin\Models\Role;
use Modules\Admin\Models\RoleMenuPermission;

class RoleService
{
    public function create(array $data): Role
    {
        return Role::create([
            'name' => $data['name'],
        ]);
    }

    public function buildEditPayload(Role $role): array
    {
        $statusPermission = Permission::firstOrCreate(['name' => 'status']);
        $viewPermission = Permission::firstOrCreate(['name' => 'view_post']);

        $permissions = Permission::query()
            ->where('name', '!=', 'status')
            ->orderBy('name')
            ->get(['id', 'name']);

        $menus = Menu::query()
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('order')
            ->get(['id', 'name', 'slug', 'route']);

        [$crudMenus, $nonCrudMenus] = $this->splitMenusByCrudCapability($menus);

        $currentPermissions = RoleMenuPermission::query()
            ->where('role_id', $role->id)
            ->get(['menu_id', 'permission_id']);

        $crudMenuIds = $crudMenus->pluck('id')->map(fn ($id) => (int) $id)->all();

        $selectedPermissions = $currentPermissions
            ->where('permission_id', '!=', $statusPermission->id)
            ->whereIn('menu_id', $crudMenuIds)
            ->groupBy('menu_id')
            ->map(fn ($group) => $group->pluck('permission_id')->map(fn ($id) => (int) $id)->values())
            ->toArray();

        $nonCrudAccess = [];
        foreach ($nonCrudMenus as $menu) {
            $nonCrudAccess[$menu->id] = $currentPermissions
                ->contains(fn ($item) =>
                    (int) $item->menu_id === (int) $menu->id
                    && (int) $item->permission_id === (int) $viewPermission->id
                );
        }

        $statusEnabled = $currentPermissions
            ->contains(fn ($item) => (int) $item->permission_id === (int) $statusPermission->id);

        return [
            'role' => $role,
            'permissions' => $permissions,
            'permissionMenus' => $crudMenus->values(),
            'nonCrudMenus' => $nonCrudMenus->values(),
            'nonCrudAccess' => $nonCrudAccess,
            'selectedPermissions' => $selectedPermissions,
            'statusEnabled' => $statusEnabled,
        ];
    }

    public function updatePermissions(Role $role, array $validated): void
    {
        $menus = Menu::query()
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->get(['id']);

        $statusPermission = Permission::firstOrCreate(['name' => 'status']);
        $viewPermission = Permission::firstOrCreate(['name' => 'view_post']);

        $fullMenus = Menu::query()
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('order')
            ->get(['id', 'name', 'slug', 'route']);

        [$crudMenus, $nonCrudMenus] = $this->splitMenusByCrudCapability($fullMenus);
        $crudMenuIds = $crudMenus->pluck('id')->map(fn ($id) => (int) $id)->all();
        $nonCrudMenuIds = $nonCrudMenus->pluck('id')->map(fn ($id) => (int) $id)->all();

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

        $nonCrudInput = data_get($validated, 'global_permissions.non_crud', []);
        foreach ($nonCrudMenuIds as $menuId) {
            $enabled = filter_var(
                data_get($nonCrudInput, (string) $menuId, data_get($nonCrudInput, $menuId, false)),
                FILTER_VALIDATE_BOOLEAN
            );

            if ($enabled) {
                RoleMenuPermission::query()
                    ->where('role_id', $role->id)
                    ->where('menu_id', $menuId)
                    ->whereNotIn('permission_id', [$statusPermission->id, $viewPermission->id])
                    ->delete();

                RoleMenuPermission::updateOrCreate(
                    [
                        'role_id' => $role->id,
                        'menu_id' => $menuId,
                        'permission_id' => $viewPermission->id,
                    ],
                    []
                );

                continue;
            }

            RoleMenuPermission::query()
                ->where('role_id', $role->id)
                ->where('menu_id', $menuId)
                ->where('permission_id', '!=', $statusPermission->id)
                ->delete();
        }

        $inputPermissions = data_get($validated, 'permissions', []);
        $currentPermissions = RoleMenuPermission::query()
            ->where('role_id', $role->id)
            ->get();

        $newMap = [];

        foreach ($inputPermissions as $menuId => $permissionIds) {
            $menuId = (int) $menuId;
            if (!in_array($menuId, $crudMenuIds, true)) {
                continue;
            }

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

            if (in_array((int) $perm->menu_id, $nonCrudMenuIds, true)) {
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
    }

    private function splitMenusByCrudCapability(Collection $menus): array
    {
        $namedRoutes = collect(Route::getRoutes()->getRoutesByName());
        $moduleCrudMap = $this->buildModuleCrudMap($namedRoutes);

        $crudMenus = collect();
        $nonCrudMenus = collect();

        foreach ($menus as $menu) {
            $routeName = (string) ($menu->route ?? '');
            $route = $routeName !== '' ? $namedRoutes->get($routeName) : null;
            $moduleKey = $route ? $this->extractModuleKeyFromUri($route->uri()) : null;
            $isCrudMenu = $moduleKey ? (bool) ($moduleCrudMap[$moduleKey] ?? false) : false;

            if ($isCrudMenu) {
                $crudMenus->push($menu);
                continue;
            }

            $nonCrudMenus->push($menu);
        }

        return [$crudMenus, $nonCrudMenus];
    }

    private function buildModuleCrudMap(Collection $namedRoutes): array
    {
        $map = [];

        foreach ($namedRoutes as $name => $route) {
            if (!$route) {
                continue;
            }

            $moduleKey = $this->extractModuleKeyFromUri($route->uri());
            if (!$moduleKey) {
                continue;
            }

            if (!isset($map[$moduleKey])) {
                $map[$moduleKey] = false;
            }

            if ($this->routeLooksCrud((string) $name, $route->uri())) {
                $map[$moduleKey] = true;
            }
        }

        return $map;
    }

    private function extractModuleKeyFromUri(string $uri): ?string
    {
        $segments = array_values(array_filter(explode('/', trim($uri, '/'))));
        if (empty($segments)) {
            return null;
        }

        if (($segments[0] ?? null) === 'verdurao') {
            return $segments[1] ?? null;
        }

        return $segments[0] ?? null;
    }

    private function routeLooksCrud(string $routeName, string $uri): bool
    {
        $name = Str::lower($routeName);
        $normalizedUri = Str::lower(trim($uri, '/'));

        if (preg_match('/\.(create|store|edit|update|destroy)$/', $name)) {
            return true;
        }

        if (preg_match('/\.(cadastro|editar|salvareditar)$/', $name)) {
            return true;
        }

        if (preg_match('/\.inserir[a-z0-9_]*$/', $name)) {
            return true;
        }

        if (
            Str::contains($normalizedUri, '/create')
            || Str::contains($normalizedUri, '/edit/')
            || Str::contains($normalizedUri, '/cadastro')
            || Str::contains($normalizedUri, '/editar/')
        ) {
            return true;
        }

        return false;
    }
}
