<?php

namespace App\Providers;

use App\Models\Produto;
use App\Repositories\CategoriaRepository;
use App\Repositories\CategoriaRepositoryEloquent;
use App\Repositories\ClienteRepository;
use App\Repositories\ClienteRepositoryEloquent;
use App\Repositories\EstoqueRepository;
use App\Repositories\EstoqueRepositoryEloquent;
use App\Repositories\ProdutoRepository;
use App\Repositories\RoleRepository;
use App\Repositories\RoleRepositoryEloquent;
use App\Repositories\TaxRuleRepository;
use App\Repositories\TaxRuleRepositoryEloquent;
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
        $this->app->bind(ProdutoRepository::class , function($app){
            return new ProdutoRepository(new Produto());
        });
        $this->app->bind(CategoriaRepository::class, CategoriaRepositoryEloquent::class);
        $this->app->bind(EstoqueRepository::class, EstoqueRepositoryEloquent::class);
        $this->app->bind(UnidadesRepository::class, UnidadesRepositoryEloquent::class);
        $this->app->bind(RoleRepository::class, RoleRepositoryEloquent::class);
        $this->app->bind(ClienteRepository::class, ClienteRepositoryEloquent::class);
        $this->app->bind(TaxRuleRepository::class, TaxRuleRepositoryEloquent::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        Paginator::defaultView('pagination::tailwind');
        Paginator::defaultSimpleView('pagination::simple-tailwind');
    }
}
