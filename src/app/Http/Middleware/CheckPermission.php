<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permissionName, string $menuSlug): Response
    {
        $user = $request->user();
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        if (! Gate::allows('has-permission', [$menuSlug, $permissionName])) {
            abort(Response::HTTP_FORBIDDEN, 'Nao autorizado.');
        }

        return $next($request);
    }
}