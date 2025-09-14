<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

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
        'menus' => fn () => \App\Models\Menu::with(['children' => function($q){
                            $q->orderBy('order');
                        }])
                        ->whereNull('parent_id')
                        ->orderBy('order')
                        ->get(['id','name','slug','icon','route','parent_id','order']),
    ];
}

   public function rootView(Request $request): string
{
    return 'app'; // uma root para tudo
}
}
