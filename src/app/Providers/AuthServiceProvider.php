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

        Gate::define('has-permission', function ($user, string $menuSlug, string $permissionName): bool {
            /** @var \App\Models\User|\Modules\Admin\Models\User $user */
            return method_exists($user, 'hasPermission') && $user->hasPermission($menuSlug, $permissionName);
        });
    }
}
