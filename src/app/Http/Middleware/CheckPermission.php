<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle($request, Closure $next, $permission, $menu)
    {
        if (
            Auth::check() &&
            (
                Auth::user()->id === 1 ||
                Auth::user()->hasPermission($menu, $permission)
            )
        ) {
            return $next($request);
        }

        abort(403, 'Acesso não autorizado');
    }
}