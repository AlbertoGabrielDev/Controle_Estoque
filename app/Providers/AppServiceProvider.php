<?php

namespace App\Providers;

use App\Models\Produto;
use App\Repositories\CategoriaRepository;
use App\Repositories\CategoriaRepositoryEloquent;
use App\Repositories\ProdutoRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProdutoRepository::class , function($app){
            return new ProdutoRepository(new Produto());
        });
        $this->app->bind(CategoriaRepository::class, CategoriaRepositoryEloquent::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
    }
}
