<?php

namespace App\Providers;

use App\Models\Produto;
use App\Repositories\CategoriaRepository;
use App\Repositories\CategoriaRepositoryEloquent;
use App\Repositories\EstoqueRepository;
use App\Repositories\EstoqueRepositoryEloquent;
use App\Repositories\ProdutoRepository;
use App\Repositories\ProdutoRepositoryEloquent;
use App\Repositories\UnidadesRepository;
use App\Repositories\UnidadesRepositoryEloquent;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    
        $this->app->bind(CategoriaRepository::class, CategoriaRepositoryEloquent::class);
        $this->app->bind(ProdutoRepository::class, ProdutoRepositoryEloquent::class);
        $this->app->bind(EstoqueRepository::class, EstoqueRepositoryEloquent::class);
        $this->app->bind(UnidadesRepository::class, UnidadesRepositoryEloquent::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
    }
}
