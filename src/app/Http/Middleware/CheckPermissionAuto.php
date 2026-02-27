<?php

namespace App\Http\Middleware;

use App\Models\Menu;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissionAuto
{
    private static ?array $routeMenuMap = null;
    private static ?array $routeMenuPrefixMap = null;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return $next($request);
        }

        $route = $request->route();
        if (!$route) {
            return $next($request);
        }

        foreach ((array) $route->gatherMiddleware() as $middleware) {
            if (is_string($middleware) && str_starts_with($middleware, 'check.permission:')) {
                return $next($request);
            }
        }

        $permissionName = $this->resolvePermissionName($request, $route);
        $menuSlug = $this->resolveMenuSlug($route);

        if (!$permissionName || !$menuSlug) {
            abort(Response::HTTP_FORBIDDEN, 'Nao autorizado.');
        }

        if (! Gate::allows('has-permission', [$menuSlug, $permissionName])) {
            abort(Response::HTTP_FORBIDDEN, 'Nao autorizado.');
        }

        return $next($request);
    }

    private function resolvePermissionName(Request $request, $route): ?string
    {
        $defaults = method_exists($route, 'getDefaults') ? $route->getDefaults() : [];
        $defaultName = $defaults['permission.name'] ?? null;
        if (is_string($defaultName) && $defaultName !== '') {
            return $defaultName;
        }

        $method = strtoupper($request->getMethod());
        $name = Str::lower((string) $route->getName());
        $uri = Str::lower((string) $route->uri());

        if (Str::contains($name, '.status') || Str::contains($uri, '/status')) {
            return 'status';
        }

        if ($method === 'DELETE' || Str::contains($name, '.destroy') || Str::contains($uri, '/delete')) {
            return 'delete_post';
        }

        if (
            in_array($method, ['PUT', 'PATCH'], true)
            || Str::contains($name, ['.editar', '.edit', '.update', '.salvareditar'])
        ) {
            return 'edit_post';
        }

        if ($method === 'POST') {
            return 'create_post';
        }

        return 'view_post';
    }

    private function resolveMenuSlug($route): ?string
    {
        $defaults = method_exists($route, 'getDefaults') ? $route->getDefaults() : [];
        $defaultMenu = $defaults['permission.menu'] ?? null;
        if (is_string($defaultMenu) && $defaultMenu !== '') {
            return $defaultMenu;
        }

        $routeName = $route->getName();
        if (is_string($routeName) && $routeName !== '') {
            $menuSlug = $this->menuSlugFromRouteName($routeName);
            if (is_string($menuSlug) && $menuSlug !== '') {
                return $menuSlug;
            }
        }

        $segments = array_values(array_filter(explode('/', trim((string) $route->uri(), '/'))));
        if (!empty($segments)) {
            if ($segments[0] === 'verdurao') {
                return $this->normalizeMenuSlug($segments[1] ?? $segments[0]);
            }

            return $this->normalizeMenuSlug($segments[0]);
        }

        if (is_string($routeName) && $routeName !== '') {
            return $this->normalizeMenuSlug(Str::before($routeName, '.'));
        }

        return null;
    }

    private function menuSlugFromRouteName(string $routeName): ?string
    {
        if (self::$routeMenuMap === null) {
            self::$routeMenuMap = Menu::query()
                ->whereNotNull('route')
                ->where('route', '!=', '')
                ->pluck('slug', 'route')
                ->all();

            self::$routeMenuPrefixMap = [];
            foreach (self::$routeMenuMap as $route => $slug) {
                $prefix = Str::before((string) $route, '.');
                if ($prefix !== '' && !isset(self::$routeMenuPrefixMap[$prefix])) {
                    self::$routeMenuPrefixMap[$prefix] = $slug;
                }
            }
        }

        $menuSlug = self::$routeMenuMap[$routeName] ?? null;
        if (!is_string($menuSlug) || $menuSlug === '') {
            $prefix = Str::before($routeName, '.');
            $menuSlug = self::$routeMenuPrefixMap[$prefix] ?? null;
            if (!is_string($menuSlug) || $menuSlug === '') {
                return null;
            }
        }

        return $this->normalizeMenuSlug($menuSlug);
    }

    private function normalizeMenuSlug(string $menuSlug): string
    {
        $aliases = [
            'usuario' => 'perfil',
            'roles' => 'permissao',
        ];

        $slug = mb_strtolower(trim($menuSlug));

        return $aliases[$slug] ?? $slug;
    }
}
