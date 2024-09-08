<?php

namespace App\Providers;

use App\Models\Fornecedor;
use App\Models\Produto;
use App\Repositories\CategoriaRepository;
use App\Repositories\CategoriaRepositoryEloquent;
use App\Repositories\EstoqueRepository;
use App\Repositories\EstoqueRepositoryEloquent;
use App\Repositories\FornecedorRepository;
use App\Repositories\FornecedorRepositoryEloquent;
use App\Repositories\MarcaRepository;
use App\Repositories\MarcaRepositoryEloquent;
use App\Repositories\ProdutoRepository;
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
        $this->app->bind(MarcaRepository::class, MarcaRepositoryEloquent::class);
        $this->app->bind(FornecedorRepository::class, FornecedorRepositoryEloquent::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
    }
}
