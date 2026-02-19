<?php

namespace Tests\Unit;

use App\Models\Categoria;
use App\Models\Fornecedor;
use App\Models\Marca;
use App\Models\Role;
use App\Models\Unidades;
use App\Models\User;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('phase2')]
class Phase2DatatableContractsTest extends TestCase
{
    public function test_phase_two_models_expose_datatable_columns_contract(): void
    {
        $models = [
            Categoria::class => ['id', 'c1', 'st', 'acoes'],
            Fornecedor::class => ['id', 'c1', 'c2', 'c3', 'c4', 'st', 'acoes'],
            Marca::class => ['id', 'c1', 'st', 'acoes'],
            Role::class => ['id', 'c1', 'acoes'],
            Unidades::class => ['id', 'c1', 'st', 'acoes'],
            User::class => ['id', 'c1', 'c2', 'st', 'acoes'],
        ];

        foreach ($models as $modelClass => $expectedAliases) {
            $columns = $modelClass::dtColumns();

            foreach ($expectedAliases as $alias) {
                $this->assertArrayHasKey($alias, $columns, "{$modelClass} missing {$alias}");
            }

            foreach ($columns as $alias => $cfg) {
                if (($cfg['computed'] ?? false) === true) {
                    continue;
                }

                $this->assertArrayHasKey('db', $cfg, "{$modelClass}.{$alias} missing db");
                $this->assertArrayHasKey('order', $cfg, "{$modelClass}.{$alias} missing order");
                $this->assertArrayHasKey('search', $cfg, "{$modelClass}.{$alias} missing search");
                $this->assertIsString($cfg['db']);
                $this->assertStringContainsString('.', $cfg['db']);
            }
        }
    }

    public function test_phase_two_models_expose_query_filters_contract(): void
    {
        $models = [
            Categoria::class => true,
            Fornecedor::class => true,
            Marca::class => true,
            Role::class => false,
            Unidades::class => true,
            User::class => true,
        ];

        foreach ($models as $modelClass => $expectsStatus) {
            $filters = $modelClass::dtFilters();

            $this->assertArrayHasKey('q', $filters, "{$modelClass} missing q filter");
            $this->assertSame('text', $filters['q']['type'] ?? null);
            $this->assertNotEmpty($filters['q']['columns'] ?? []);

            if ($expectsStatus) {
                $this->assertArrayHasKey('status', $filters, "{$modelClass} missing status filter");
                $this->assertSame('select', $filters['status']['type'] ?? null);
                $this->assertSame('=', $filters['status']['operator'] ?? null);
                $this->assertTrue((bool) ($filters['status']['nullable'] ?? false));
            } else {
                $this->assertArrayNotHasKey('status', $filters, "{$modelClass} should not expose status filter");
            }
        }
    }

    public function test_phase_two_vue_pages_exist_contract(): void
    {
        $expectedPages = [
            resource_path('js/Pages/Brands/Index.vue'),
            resource_path('js/Pages/Brands/Create.vue'),
            resource_path('js/Pages/Brands/Edit.vue'),
            resource_path('js/Pages/Brands/BrandForm.vue'),
            resource_path('js/Pages/Units/Index.vue'),
            resource_path('js/Pages/Units/Create.vue'),
            resource_path('js/Pages/Units/Edit.vue'),
            resource_path('js/Pages/Units/UnitForm.vue'),
            resource_path('js/Pages/Categories/Home.vue'),
            resource_path('js/Pages/Categories/Index.vue'),
            resource_path('js/Pages/Categories/Create.vue'),
            resource_path('js/Pages/Categories/Edit.vue'),
            resource_path('js/Pages/Categories/CategoryForm.vue'),
            resource_path('js/Pages/Suppliers/Index.vue'),
            resource_path('js/Pages/Suppliers/Create.vue'),
            resource_path('js/Pages/Suppliers/Edit.vue'),
            resource_path('js/Pages/Suppliers/SupplierForm.vue'),
            resource_path('js/Pages/Users/Index.vue'),
            resource_path('js/Pages/Users/Create.vue'),
            resource_path('js/Pages/Users/Edit.vue'),
            resource_path('js/Pages/Users/UserForm.vue'),
            resource_path('js/Pages/Roles/Index.vue'),
            resource_path('js/Pages/Roles/Create.vue'),
            resource_path('js/Pages/Roles/Edit.vue'),
            resource_path('js/Pages/Roles/RoleForm.vue'),
        ];

        foreach ($expectedPages as $pagePath) {
            $this->assertFileExists($pagePath);
        }
    }
}
