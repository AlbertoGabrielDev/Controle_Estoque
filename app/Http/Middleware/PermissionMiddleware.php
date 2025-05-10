<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
    
        $routeName = Route::currentRouteName();
        $menuSlug = $this->mapRouteToMenuSlug($routeName);
        $request->attributes->set('currentMenuSlug', $menuSlug);
       
        view()->share('currentMenuSlug', $menuSlug);

        return $next($request);
    }

    private function mapRouteToMenuSlug($routeName)
    {
 
        $slugMap = [
            'usuario' => 'perfil',
        ];

        if ($routeName) {
            $routePrefix = explode('.', $routeName)[0];
            return $slugMap[$routePrefix] ?? $routePrefix;
        }
        return null;
    }
}
