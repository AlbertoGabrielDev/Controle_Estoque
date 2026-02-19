<?php

namespace App\Http\Middleware;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'menus' => fn () => $this->resolveInertiaMenus($request->user()),
        ];
    }

    public function rootView(Request $request): string
    {
        return 'app';
    }

    private function resolveInertiaMenus(?User $user): array
    {
        if (!$user) {
            return [];
        }

        $menus = Menu::query()
            ->with(['children' => function ($query) {
                $query->orderBy('order');
            }])
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get(['id', 'name', 'slug', 'icon', 'route', 'parent_id', 'order']);

        return $menus
            ->map(function (Menu $menu) use ($user) {
                $children = $menu->children
                    ->filter(fn (Menu $child) => $this->canViewMenu($user, $child))
                    ->map(fn (Menu $child) => $this->serializeMenu($child, []))
                    ->values()
                    ->all();

                $canViewSelf = $this->canViewMenu($user, $menu);
                if (!$canViewSelf && empty($children)) {
                    return null;
                }

                return $this->serializeMenu($menu, $children);
            })
            ->filter()
            ->values()
            ->all();
    }

    private function canViewMenu(User $user, Menu $menu): bool
    {
        $slug = (string) ($menu->slug ?? '');

        if ($slug === '') {
            return !empty($menu->route);
        }

        return $user->hasPermission($slug, 'view_post');
    }

    private function serializeMenu(Menu $menu, array $children): array
    {
        return [
            'id' => $menu->id,
            'name' => $menu->name,
            'slug' => $menu->slug,
            'icon' => $menu->icon,
            'route' => $menu->route,
            'parent_id' => $menu->parent_id,
            'order' => $menu->order,
            'children' => $children,
        ];
    }
}
