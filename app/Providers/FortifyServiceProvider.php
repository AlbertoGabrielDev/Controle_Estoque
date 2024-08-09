<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\Unidades;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{

    public function register(): void
    {
     
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();
            $unidade= Unidades::where('id_unidade', $request->id_unidade)->first();
      
            if ($user &&
                Hash::check($request->password, $user->password) && $user->status == 1 && $unidade->status == 1) {
                    $request->session()->put('id_unidade', $request->input('id_unidade'));
                    return $user;
            }else{
                session()->flash('error', 'Senha ou email errado. Confira os dados.');
            }
            if($user->status == 0){
                return session()->flash('error', 'Usuario desativado. Fale com o Administrador');
            }
            if($unidade->status == 0){
                return session()->flash('error', 'Unidade desativada. Fale com o Administrador');
            }
            if ($user == "") {
                return session()->flash('error', 'Email não existe.');
            }
        });

    }
}
