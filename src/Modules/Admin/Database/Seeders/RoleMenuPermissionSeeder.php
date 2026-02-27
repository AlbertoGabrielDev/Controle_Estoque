<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Admin\Models\Menu;
use Modules\Admin\Models\Permission;
use Modules\Admin\Models\Role;
use Modules\Admin\Models\RoleMenuPermission;

class RoleMenuPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = Permission::query()->pluck('id', 'name');
        $menus = Menu::query()
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->get(['id', 'slug']);
        $menuMap = $menus->pluck('id', 'slug');
        $roleMap = Role::query()->pluck('id', 'name');

        $matrix = [
            'admin' => [
                '*' => ['*'],
            ],
            'gerente' => [
                '*' => ['view_post', 'create_post', 'edit_post', 'delete_post', 'status', 'export', 'import'],
            ],
            'atendente' => [
                'vendas' => ['view_post', 'create_post', 'edit_post'],
                'historico_vendas' => ['view_post'],
                'clientes' => ['view_post', 'create_post', 'edit_post'],
                'produtos' => ['view_post'],
                'estoque' => ['view_post'],
            ],
            'marketing' => [
                'dashboard' => ['view_post'],
                'calendario' => ['view_post'],
                'vendas' => ['view_post'],
                'historico_vendas' => ['view_post'],
                'clientes' => ['view_post'],
            ],
        ];

        foreach ($matrix as $roleName => $rules) {
            $roleId = $roleMap[$roleName] ?? null;
            if (!$roleId) {
                $this->command?->warn("Role '{$roleName}' nao encontrado. Pulei.");
                continue;
            }

            if (array_key_exists('*', $rules)) {
                $permissionIds = $this->resolvePermissionIds($rules['*'], $permissions);
                foreach ($menus as $menu) {
                    $this->syncPermissions($roleId, $menu->id, $permissionIds);
                }
                continue;
            }

            foreach ($rules as $menuSlug => $permissionNames) {
                $menuId = $menuMap[$menuSlug] ?? null;
                if (!$menuId) {
                    $this->command?->warn("Menu slug '{$menuSlug}' nao encontrado. Pulei.");
                    continue;
                }

                $permissionIds = $this->resolvePermissionIds($permissionNames, $permissions);
                $this->syncPermissions($roleId, $menuId, $permissionIds);
            }
        }
    }

    private function resolvePermissionIds(array $names, $permissions): array
    {
        if (in_array('*', $names, true)) {
            return $permissions->values()->all();
        }

        $ids = [];
        foreach ($names as $name) {
            if (isset($permissions[$name])) {
                $ids[] = (int) $permissions[$name];
            }
        }

        return array_values(array_unique($ids));
    }

    private function syncPermissions(int $roleId, int $menuId, array $permissionIds): void
    {
        foreach ($permissionIds as $permissionId) {
            RoleMenuPermission::updateOrCreate(
                [
                    'role_id' => $roleId,
                    'menu_id' => $menuId,
                    'permission_id' => $permissionId,
                ],
                []
            );
        }
    }
}
