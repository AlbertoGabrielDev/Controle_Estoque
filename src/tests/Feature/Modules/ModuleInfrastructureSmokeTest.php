<?php

namespace Tests\Feature\Modules;

use App\Support\Modules\ModuleRegistry;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class ModuleInfrastructureSmokeTest extends TestCase
{
    public function test_module_registry_discovers_products_module_and_paths(): void
    {
        $this->assertTrue($this->app->bound(ModuleRegistry::class));
        $this->assertSame(base_path('Modules'), config('modules.paths.root'));

        /** @var ModuleRegistry $registry */
        $registry = $this->app->make(ModuleRegistry::class);
        $modules = collect($registry->all())->keyBy('folder');

        $this->assertTrue($modules->has('Products'));

        $products = $modules->get('Products');

        $this->assertSame('products', $products['slug']);
        $this->assertDirectoryExists($products['path']);
        $this->assertDirectoryExists($products['paths']['migrations']);
        $this->assertDirectoryExists($products['paths']['seeders']);
        $this->assertDirectoryExists($products['paths']['factories']);
        $this->assertDirectoryExists($products['paths']['resources_js']);

        $discovered = collect(app('modules.discovered'))->keyBy('folder');
        $this->assertTrue($discovered->has('Products'));
    }
}
