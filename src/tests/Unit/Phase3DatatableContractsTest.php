<?php

namespace Tests\Unit;

use App\Models\Estoque;
use App\Models\Produto;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('phase3')]
class Phase3DatatableContractsTest extends TestCase
{
    public function test_phase_three_models_expose_datatable_columns_contract(): void
    {
        $models = [
            Produto::class => ['id', 'c1', 'c2', 'c3', 'c4', 'c5', 'st', 'acoes'],
            Estoque::class => ['id', 'c1', 'c2', 'c3', 'c4', 'c5', 'c6', 'c7', 'c8', 'c9', 'c10', 'st', 'acoes'],
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
            }
        }
    }

    public function test_phase_three_models_expose_query_filters_contract(): void
    {
        $models = [
            Produto::class,
            Estoque::class,
        ];

        foreach ($models as $modelClass) {
            $filters = $modelClass::dtFilters();

            $this->assertArrayHasKey('q', $filters, "{$modelClass} missing q filter");
            $this->assertSame('text', $filters['q']['type'] ?? null);
            $this->assertNotEmpty($filters['q']['columns'] ?? []);

            $this->assertArrayHasKey('status', $filters, "{$modelClass} missing status filter");
            $this->assertSame('select', $filters['status']['type'] ?? null);
        }
    }

    public function test_phase_three_vue_pages_exist_contract(): void
    {
        $expectedPages = [
            resource_path('js/Pages/Products/Index.vue'),
            resource_path('js/Pages/Products/Create.vue'),
            resource_path('js/Pages/Products/Edit.vue'),
            resource_path('js/Pages/Products/ProductForm.vue'),
            resource_path('js/Pages/Stock/Index.vue'),
            resource_path('js/Pages/Stock/Create.vue'),
            resource_path('js/Pages/Stock/Edit.vue'),
            resource_path('js/Pages/Stock/StockForm.vue'),
            resource_path('js/Pages/Stock/StockTaxPreview.vue'),
        ];

        foreach ($expectedPages as $pagePath) {
            $this->assertFileExists($pagePath);
        }
    }
}
