<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Menu;
use Illuminate\Support\Facades\View;
class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        View::composer('layouts.principal', function ($view) {
            $menus = Menu::with('children')
                ->whereNull('parent_id')
                ->orderBy('order')
                ->get();
            
            $view->with('menus', $menus);
        });
    }
}
