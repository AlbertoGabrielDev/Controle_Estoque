<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Http\Requests\LoginRequest;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use Laravel\Fortify\Fortify;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Verifica se o usuário tem uma permissão específica em um menu
        Gate::define('has-permission', function (User $user, $menuSlug, $permissionName) {
            // Admin tem acesso total
            if ($user->hasRole('admin')) {
                return true;
            }

            $permission = Permission::where('name', $permissionName)->first();
            $menu = Menu::where('slug', $menuSlug)->first();

            if (!$permission || !$menu) return false;

            return $user->roles()
                ->whereHas('roleMenuPermissions', function ($query) use ($menu, $permission) {
                    $query->where('menu_id', $menu->id)
                        ->where('permission_id', $permission->id);
                })->exists();
        });
    }
}
