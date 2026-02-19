<?php

namespace Tests\Unit;

use App\Models\Categoria;
use App\Models\CustomerSegment;
use App\Models\Fornecedor;
use App\Models\Marca;
use App\Models\Role;
use App\Models\Unidades;
use App\Models\User;
use App\Support\DataTableActions;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('phase1')]
class ExampleTest extends TestCase
{
    public function test_datatable_actions_edit_builds_expected_link(): void
    {
        $html = DataTableActions::edit('clientes.edit', 42);

        $this->assertStringContainsString('href="'.e(route('clientes.edit', 42)).'"', $html);
        $this->assertStringContainsString('fa-edit', $html);
        $this->assertStringContainsString('title="Editar"', $html);
    }

    public function test_datatable_actions_status_builds_expected_markup_for_active_and_inactive_states(): void
    {
        $activeHtml = DataTableActions::status('cliente.status', 'cliente', 7, true);
        $inactiveHtml = DataTableActions::status('cliente.status', 'cliente', 7, false);
        $expectedUrl = route('cliente.status', ['modelName' => 'cliente', 'id' => 7]);

        $this->assertStringContainsString('data-url="'.e($expectedUrl).'"', $activeHtml);
        $this->assertStringContainsString('data-active="1"', $activeHtml);
        $this->assertStringContainsString('bg-green-500', $activeHtml);
        $this->assertStringContainsString('aria-pressed="true"', $activeHtml);

        $this->assertStringContainsString('data-url="'.e($expectedUrl).'"', $inactiveHtml);
        $this->assertStringContainsString('data-active="0"', $inactiveHtml);
        $this->assertStringContainsString('bg-red-400', $inactiveHtml);
        $this->assertStringContainsString('aria-pressed="false"', $inactiveHtml);
    }

    public function test_datatable_actions_delete_builds_delete_form_contract(): void
    {
        $html = DataTableActions::delete('taxes.destroy', 9, 'Excluir', 'Excluir esta regra?');

        $this->assertStringContainsString('method="POST"', $html);
        $this->assertStringContainsString('action="'.e(route('taxes.destroy', 9)).'"', $html);
        $this->assertStringContainsString('name="_token"', $html);
        $this->assertStringContainsString('name="_method" value="DELETE"', $html);
        $this->assertStringContainsString('Excluir esta regra?', $html);
    }

    public function test_datatable_actions_wrap_contract(): void
    {
        $empty = DataTableActions::wrap([]);
        $filled = DataTableActions::wrap(['<span>ok</span>'], 'end');

        $this->assertStringContainsString('opacity-0', $empty);
        $this->assertStringContainsString('justify-end', $filled);
        $this->assertStringContainsString('<span>ok</span>', $filled);
    }

    public function test_phase_one_models_expose_datatable_columns_contract(): void
    {
        $models = [
            CustomerSegment::class => ['id', 'c1', 'acoes'],
            Categoria::class => ['id', 'c1', 'st', 'acoes'],
            Fornecedor::class => ['id', 'c1', 'c2', 'c3', 'c4', 'st', 'acoes'],
            Marca::class => ['id', 'c1', 'st', 'acoes'],
            Unidades::class => ['id', 'c1', 'st', 'acoes'],
            Role::class => ['id', 'c1', 'acoes'],
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

    public function test_phase_one_models_expose_query_filters_contract(): void
    {
        $models = [
            CustomerSegment::class => false,
            Categoria::class => true,
            Fornecedor::class => true,
            Marca::class => true,
            Unidades::class => true,
            Role::class => false,
            User::class => true,
        ];

        foreach ($models as $modelClass => $expectsStatusFilter) {
            $filters = $modelClass::dtFilters();

            $this->assertArrayHasKey('q', $filters, "{$modelClass} missing q filter");
            $this->assertSame('text', $filters['q']['type'] ?? null);
            $this->assertNotEmpty($filters['q']['columns'] ?? []);

            if ($expectsStatusFilter) {
                $this->assertArrayHasKey('status', $filters, "{$modelClass} missing status filter");
                $this->assertSame('select', $filters['status']['type'] ?? null);
            } else {
                $this->assertArrayNotHasKey('status', $filters, "{$modelClass} should not expose status filter");
            }
        }
    }
}
