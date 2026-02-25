<?php

namespace Tests\Feature\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class AdminModulePhaseSixRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_whatsapp_routes_are_not_registered(): void
    {
        $uris = collect(Route::getRoutes())->map(fn ($route) => strtolower($route->uri()));
        $names = collect(Route::getRoutes())->map(fn ($route) => strtolower((string) $route->getName()));

        $this->assertTrue($uris->every(fn ($uri) => !str_contains($uri, 'wpp') && !str_contains($uri, 'whatsapp')));
        $this->assertTrue($names->every(fn ($name) => $name === '' || (!str_contains($name, 'wpp') && !str_contains($name, 'whatsapp'))));
    }
}
