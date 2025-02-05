<?php

namespace App\Providers;

use App\Models\Permission;
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

     public function boot(): void
    {
        // Gate::define('permissao',function(User $user){
        //     $role = $user->roles->first();
           
        //     if ($role && $role->name === 'admin') {
        //         return true;
        //     }
    
        //     return false;
        // });

        // Gate::define('view_post', function(User $user) {
        //     $role = $user->roles->first();
        
        //     if ($role && in_array($role->name, ['admin', 'gerente', 'marketing', 'atendente','Vendedor'])) {
        //         return true;
        //     }
        
        //     return false;
        // });

        // Gate::define('edit_post', function(User $user) {
        //     $role = $user->roles->first();
        
        //     if ($role && in_array($role->name, ['admin', 'gerente', 'marketing'])) {
        //         return true;
        //     }
        
        //     return false;
        // });

        // Gate::define('create_post', function(User $user) {
        //     $role = $user->roles->first();
        
        //     if ($role && in_array($role->name, ['admin', 'gerente', 'marketing','Vendedor'])) {
        //         return true;
        //     }
        
        //     return false;
        // });

        // Gate::define('create_user','delete_user', function(User $user) {
        //     $role = $user->roles->first();
        
        //     if ($role && in_array($role->name, ['admin', 'gerente'])) {
        //         return true;
        //     }
        
        //     return false;
        // });

        $this->registerPolicies();
        
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
        });
    
        try {
            Permission::all()->each(function ($permission) {
                Gate::define($permission->name, function ($user) use ($permission) {
                    return $user->hasPermission($permission->name);
                });
            });
        } catch (\Exception $e) {
            // Tratar exceção se a tabela não existir
        }
    }
}
