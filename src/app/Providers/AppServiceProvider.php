<?php

namespace App\Providers;

use App\Repositories\CategoriaRepository;
use App\Repositories\CategoriaRepositoryEloquent;
use App\Repositories\ClienteRepository;
use App\Repositories\ClienteRepositoryEloquent;
use App\Repositories\EstoqueRepository;
use App\Repositories\EstoqueRepositoryEloquent;
use App\Repositories\ProdutoRepository;
use App\Repositories\ProdutoRepositoryEloquent;
use App\Repositories\RoleRepository;
use App\Repositories\RoleRepositoryEloquent;
use App\Repositories\TabelaPrecoRepository;
use App\Repositories\TabelaPrecoRepositoryEloquent;
use App\Repositories\TaxRuleRepository;
use App\Repositories\TaxRuleRepositoryEloquent;
use App\Repositories\UnidadesRepository;
use App\Repositories\UnidadesRepositoryEloquent;
use Illuminate\Support\ServiceProvider;
use Modules\Customers\Repositories\ClienteRepository as ModuleClienteRepository;
use Modules\Customers\Repositories\ClienteRepositoryEloquent as ModuleClienteRepositoryEloquent;
use Modules\Sales\Repositories\CartRepository as ModuleCartRepository;
use Modules\Sales\Repositories\CartRepositoryEloquent as ModuleCartRepositoryEloquent;
use Modules\Sales\Repositories\OrderRepository as ModuleOrderRepository;
use Modules\Sales\Repositories\OrderRepositoryEloquent as ModuleOrderRepositoryEloquent;
use Modules\Sales\Repositories\VendaRepository as ModuleVendaRepository;
use Modules\Sales\Repositories\VendaRepositoryEloquent as ModuleVendaRepositoryEloquent;
use Modules\PriceTables\Repositories\TabelaPrecoRepository as ModuleTabelaPrecoRepository;
use Modules\PriceTables\Repositories\TabelaPrecoRepositoryEloquent as ModuleTabelaPrecoRepositoryEloquent;
use Modules\Products\Repositories\ProdutoRepository as ModuleProdutoRepository;
use Modules\Products\Repositories\ProdutoRepositoryEloquent as ModuleProdutoRepositoryEloquent;
use Modules\Stock\Repositories\EstoqueRepository as ModuleEstoqueRepository;
use Modules\Stock\Repositories\EstoqueRepositoryEloquent as ModuleEstoqueRepositoryEloquent;

use Illuminate\Pagination\Paginator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProdutoRepository::class, ProdutoRepositoryEloquent::class);
        $this->app->bind(ModuleProdutoRepository::class, ModuleProdutoRepositoryEloquent::class);
        $this->app->bind(CategoriaRepository::class, CategoriaRepositoryEloquent::class);
        $this->app->bind(EstoqueRepository::class, EstoqueRepositoryEloquent::class);
        $this->app->bind(ModuleEstoqueRepository::class, ModuleEstoqueRepositoryEloquent::class);
        $this->app->bind(TabelaPrecoRepository::class, TabelaPrecoRepositoryEloquent::class);
        $this->app->bind(ModuleTabelaPrecoRepository::class, ModuleTabelaPrecoRepositoryEloquent::class);
        $this->app->bind(ModuleCartRepository::class, ModuleCartRepositoryEloquent::class);
        $this->app->bind(ModuleOrderRepository::class, ModuleOrderRepositoryEloquent::class);
        $this->app->bind(ModuleVendaRepository::class, ModuleVendaRepositoryEloquent::class);
        $this->app->bind(UnidadesRepository::class, UnidadesRepositoryEloquent::class);
        $this->app->bind(RoleRepository::class, RoleRepositoryEloquent::class);
        $this->app->bind(ClienteRepository::class, ClienteRepositoryEloquent::class);
        $this->app->bind(ModuleClienteRepository::class, ModuleClienteRepositoryEloquent::class);
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
