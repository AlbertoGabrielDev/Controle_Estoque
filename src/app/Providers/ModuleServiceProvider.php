<?php

namespace App\Providers;

use App\Support\Modules\ModuleRegistry;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/modules.php', 'modules');

        $this->app->singleton(ModuleRegistry::class, function ($app) {
            return new ModuleRegistry($app['files']);
        });

        $this->app->alias(ModuleRegistry::class, 'modules.registry');
    }

    public function boot(): void
    {
        /** @var ModuleRegistry $registry */
        $registry = $this->app->make(ModuleRegistry::class);
        $modules = $registry->all();

        $this->app->instance('modules.discovered', $modules);
        $this->app->instance('modules.paths.migrations', $registry->existingPaths('migrations'));
        $this->app->instance('modules.paths.seeders', $registry->existingPaths('seeders'));
        $this->app->instance('modules.paths.factories', $registry->existingPaths('factories'));

        if ((bool) config('modules.autoload.migrations', true)) {
            foreach ($registry->existingPaths('migrations') as $path) {
                $this->loadMigrationsFrom($path);
            }
        }

        if ((bool) config('modules.autoload.views', true)) {
            foreach ($modules as $module) {
                $viewsPath = $module['paths']['views'] ?? null;
                if (is_string($viewsPath) && is_dir($viewsPath)) {
                    $this->loadViewsFrom($viewsPath, (string) $module['slug']);
                }
            }
        }

        if ((bool) config('modules.autoload.routes', true) && !$this->app->routesAreCached()) {
            foreach ($modules as $module) {
                $routesPath = $module['paths']['routes'] ?? null;
                if (!is_string($routesPath) || !is_dir($routesPath)) {
                    continue;
                }

                foreach (['web.php', 'api.php'] as $file) {
                    $fullPath = $routesPath . DIRECTORY_SEPARATOR . $file;
                    if (is_file($fullPath)) {
                        $this->loadRoutesFrom($fullPath);
                    }
                }
            }
        }
    }
}
