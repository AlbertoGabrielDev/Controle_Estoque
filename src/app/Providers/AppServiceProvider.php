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
use Modules\Admin\Repositories\RoleRepository as ModuleRoleRepository;
use Modules\Admin\Repositories\RoleRepositoryEloquent as ModuleRoleRepositoryEloquent;
use Modules\Taxes\Repositories\TaxRuleRepository as ModuleTaxRuleRepository;
use Modules\Taxes\Repositories\TaxRuleRepositoryEloquent as ModuleTaxRuleRepositoryEloquent;
use Modules\Purchases\Repositories\PurchaseRequisitionRepository as ModulePurchaseRequisitionRepository;
use Modules\Purchases\Repositories\PurchaseRequisitionRepositoryEloquent as ModulePurchaseRequisitionRepositoryEloquent;
use Modules\Purchases\Repositories\PurchaseOrderRepository as ModulePurchaseOrderRepository;
use Modules\Purchases\Repositories\PurchaseOrderRepositoryEloquent as ModulePurchaseOrderRepositoryEloquent;
use Modules\Purchases\Repositories\PurchaseReceiptRepository as ModulePurchaseReceiptRepository;
use Modules\Purchases\Repositories\PurchaseReceiptRepositoryEloquent as ModulePurchaseReceiptRepositoryEloquent;
use Modules\Purchases\Repositories\PurchasePayableRepository as ModulePurchasePayableRepository;
use Modules\Purchases\Repositories\PurchasePayableRepositoryEloquent as ModulePurchasePayableRepositoryEloquent;
use Modules\Purchases\Repositories\PurchaseQuotationRepository as ModulePurchaseQuotationRepository;
use Modules\Purchases\Repositories\PurchaseQuotationRepositoryEloquent as ModulePurchaseQuotationRepositoryEloquent;
use Modules\Purchases\Repositories\PurchaseReturnRepository as ModulePurchaseReturnRepository;
use Modules\Purchases\Repositories\PurchaseReturnRepositoryEloquent as ModulePurchaseReturnRepositoryEloquent;
use Modules\Brands\Repositories\MarcaRepository;
use Modules\Brands\Repositories\MarcaRepositoryEloquent;
use Modules\Categories\Repositories\CategoriaRepository as ModuleCategoriaRepository;
use Modules\Categories\Repositories\CategoriaRepositoryEloquent as ModuleCategoriaRepositoryEloquent;
use Modules\Customers\Repositories\CustomerSegmentRepository;
use Modules\Customers\Repositories\CustomerSegmentRepositoryEloquent;
use Modules\Finance\Repositories\CentroCustoRepository;
use Modules\Finance\Repositories\CentroCustoRepositoryEloquent;
use Modules\Finance\Repositories\ContaContabilRepository;
use Modules\Finance\Repositories\ContaContabilRepositoryEloquent;
use Modules\Finance\Repositories\DespesaRepository;
use Modules\Finance\Repositories\DespesaRepositoryEloquent;
use Modules\Items\Repositories\ItemRepository;
use Modules\Items\Repositories\ItemRepositoryEloquent;
use Modules\MeasureUnits\Repositories\UnidadeMedidaRepository;
use Modules\MeasureUnits\Repositories\UnidadeMedidaRepositoryEloquent;
use Modules\Suppliers\Repositories\FornecedorRepository;
use Modules\Suppliers\Repositories\FornecedorRepositoryEloquent;
use Modules\Units\Repositories\UnidadesRepository as ModuleUnidadesRepository;
use Modules\Units\Repositories\UnidadesRepositoryEloquent as ModuleUnidadesRepositoryEloquent;
use Modules\Commercial\Repositories\CommercialOpportunityRepository;
use Modules\Commercial\Repositories\CommercialOpportunityRepositoryEloquent;
use Modules\Commercial\Repositories\CommercialProposalRepository;
use Modules\Commercial\Repositories\CommercialProposalRepositoryEloquent;
use Modules\Commercial\Repositories\CommercialDiscountPolicyRepository;
use Modules\Commercial\Repositories\CommercialDiscountPolicyRepositoryEloquent;
use Modules\Commercial\Repositories\CommercialSalesOrderRepository;
use Modules\Commercial\Repositories\CommercialSalesOrderRepositoryEloquent;
use Modules\Commercial\Repositories\CommercialSalesInvoiceRepository;
use Modules\Commercial\Repositories\CommercialSalesInvoiceRepositoryEloquent;
use Modules\Commercial\Repositories\CommercialSalesReturnRepository;
use Modules\Commercial\Repositories\CommercialSalesReturnRepositoryEloquent;
use Modules\Commercial\Repositories\CommercialSalesReceivableRepository;
use Modules\Commercial\Repositories\CommercialSalesReceivableRepositoryEloquent;
use Modules\Commercial\Repositories\CommercialDocumentSequenceRepository;
use Modules\Commercial\Repositories\CommercialDocumentSequenceRepositoryEloquent;

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
        $this->app->bind(ModuleRoleRepository::class, ModuleRoleRepositoryEloquent::class);
        $this->app->bind(ClienteRepository::class, ClienteRepositoryEloquent::class);
        $this->app->bind(ModuleClienteRepository::class, ModuleClienteRepositoryEloquent::class);
        $this->app->bind(TaxRuleRepository::class, TaxRuleRepositoryEloquent::class);
        $this->app->bind(ModuleTaxRuleRepository::class, ModuleTaxRuleRepositoryEloquent::class);
        $this->app->bind(ModulePurchaseRequisitionRepository::class, ModulePurchaseRequisitionRepositoryEloquent::class);
        $this->app->bind(ModulePurchaseOrderRepository::class, ModulePurchaseOrderRepositoryEloquent::class);
        $this->app->bind(ModulePurchaseReceiptRepository::class, ModulePurchaseReceiptRepositoryEloquent::class);
        $this->app->bind(ModulePurchasePayableRepository::class, ModulePurchasePayableRepositoryEloquent::class);
        $this->app->bind(ModulePurchaseQuotationRepository::class, ModulePurchaseQuotationRepositoryEloquent::class);
        $this->app->bind(ModulePurchaseReturnRepository::class, ModulePurchaseReturnRepositoryEloquent::class);

        $this->app->bind(MarcaRepository::class, MarcaRepositoryEloquent::class);
        $this->app->bind(ModuleCategoriaRepository::class, ModuleCategoriaRepositoryEloquent::class);
        $this->app->bind(CustomerSegmentRepository::class, CustomerSegmentRepositoryEloquent::class);
        $this->app->bind(CentroCustoRepository::class, CentroCustoRepositoryEloquent::class);
        $this->app->bind(ContaContabilRepository::class, ContaContabilRepositoryEloquent::class);
        $this->app->bind(DespesaRepository::class, DespesaRepositoryEloquent::class);
        $this->app->bind(ItemRepository::class, ItemRepositoryEloquent::class);
        $this->app->bind(UnidadeMedidaRepository::class, UnidadeMedidaRepositoryEloquent::class);
        $this->app->bind(FornecedorRepository::class, FornecedorRepositoryEloquent::class);
        $this->app->bind(ModuleUnidadesRepository::class, ModuleUnidadesRepositoryEloquent::class);
        $this->app->bind(CommercialOpportunityRepository::class, CommercialOpportunityRepositoryEloquent::class);
        $this->app->bind(CommercialProposalRepository::class, CommercialProposalRepositoryEloquent::class);
        $this->app->bind(CommercialDiscountPolicyRepository::class, CommercialDiscountPolicyRepositoryEloquent::class);
        $this->app->bind(CommercialSalesOrderRepository::class, CommercialSalesOrderRepositoryEloquent::class);
        $this->app->bind(CommercialSalesInvoiceRepository::class, CommercialSalesInvoiceRepositoryEloquent::class);
        $this->app->bind(CommercialSalesReturnRepository::class, CommercialSalesReturnRepositoryEloquent::class);
        $this->app->bind(CommercialSalesReceivableRepository::class, CommercialSalesReceivableRepositoryEloquent::class);
        $this->app->bind(CommercialDocumentSequenceRepository::class, CommercialDocumentSequenceRepositoryEloquent::class);
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
