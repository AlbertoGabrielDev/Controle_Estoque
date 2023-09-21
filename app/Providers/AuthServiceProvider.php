<?php

namespace App\Providers;

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
        Gate::define('permissao',function(User $user){
            return $user->id ===1;
        });

        Fortify::authenticateUsing(function (LoginRequest $request) {
           
            $user = User::where('email', $request->email)->first();
           
            if ($user &&
                Hash::check($request->password, $user->password) && $user->status === 1 ) {
               return $user;
            }
        });
    }
}
