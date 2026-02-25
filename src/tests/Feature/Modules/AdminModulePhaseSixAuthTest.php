<?php

namespace Tests\Feature\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\View\View;
use Modules\Admin\Http\Controllers\UsuarioController;
use Modules\Admin\Models\Menu;
use Modules\Admin\Models\Permission;
use Modules\Admin\Models\Role;
use Modules\Admin\Models\RoleMenuPermission;
use Modules\Admin\Models\User;
use Modules\Units\Models\Unidades;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class AdminModulePhaseSixAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_view_exposes_units(): void
    {
        $user = User::factory()->create();

        $unit = Unidades::query()->create([
            'nome' => 'Unidade Teste',
            'status' => 1,
            'id_users_fk' => $user->id,
        ]);

        $controller = app(UsuarioController::class);
        $response = $controller->unidade();

        $this->assertInstanceOf(View::class, $response);
        $this->assertSame('auth.login', $response->getName());

        $units = $response->getData()['units'] ?? [];
        $this->assertCount(1, $units);
        $this->assertSame($unit->id_unidade, $units[0]->id_unidade ?? null);
    }

    public function test_user_permission_checks_role_menu_permission(): void
    {
        $menu = Menu::query()->create([
            'name' => 'Clientes',
            'route' => 'clientes.index',
            'order' => 1,
        ]);
        $menu->slug = 'clientes';
        $menu->save();
        $permission = Permission::query()->create(['name' => 'view_post']);
        $role = Role::query()->create(['name' => 'Tester']);

        RoleMenuPermission::query()->create([
            'role_id' => $role->id,
            'menu_id' => $menu->id,
            'permission_id' => $permission->id,
        ]);

        $user = User::factory()->create();
        $user->roles()->sync([$role->id]);

        $this->assertTrue($user->hasPermission('clientes', 'view_post'));
        $this->assertFalse($user->hasPermission('clientes', 'edit_post'));
    }
}
