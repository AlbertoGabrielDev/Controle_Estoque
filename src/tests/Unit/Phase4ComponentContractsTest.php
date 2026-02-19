<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('phase4')]
class Phase4ComponentContractsTest extends TestCase
{
    public function test_phase_four_vue_pages_and_components_exist_contract(): void
    {
        $expectedFiles = [
            resource_path('js/Pages/Sales/Index.vue'),
            resource_path('js/Pages/Sales/ClientSelector.vue'),
            resource_path('js/Pages/Sales/ManualCodeInput.vue'),
            resource_path('js/Pages/Sales/QrScanner.vue'),
            resource_path('js/Pages/Sales/CartTable.vue'),
            resource_path('js/Pages/Sales/RecentSalesTable.vue'),
            resource_path('js/composables/useCart.js'),
            resource_path('js/Pages/Spreadsheets/Index.vue'),
            resource_path('js/Pages/Spreadsheets/FileInputCard.vue'),
            resource_path('js/Pages/Spreadsheets/SpreadsheetPreviewTable.vue'),
            resource_path('js/Pages/Spreadsheets/ComparisonPanel.vue'),
        ];

        foreach ($expectedFiles as $file) {
            $this->assertFileExists($file);
        }
    }

    public function test_phase_four_controllers_render_inertia_contract(): void
    {
        $vendaController = file_get_contents(app_path('Http/Controllers/VendaController.php'));
        $spreadsheetController = file_get_contents(app_path('Http/Controllers/SpreadsheetController.php'));

        $this->assertStringContainsString("Inertia::render('Sales/Index'", $vendaController);
        $this->assertStringContainsString("Inertia::render('Spreadsheets/Index'", $spreadsheetController);
    }
}
