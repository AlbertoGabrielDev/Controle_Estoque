<?php

namespace Tests\Unit\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Purchases\Models\PurchaseDocumentSequence;
use Modules\Purchases\Services\DocumentNumberService;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class DocumentNumberServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure document numbers increment and are formatted correctly.
     *
     * @return void
     * @throws \Throwable
     */
    public function test_generate_increments_and_formats_sequence(): void
    {
        $service = app(DocumentNumberService::class);

        $first = $service->generate('REQ');
        $second = $service->generate('REQ');

        $this->assertSame('REQ-000001', $first);
        $this->assertSame('REQ-000002', $second);

        $sequence = PurchaseDocumentSequence::query()->where('type', 'REQ')->first();
        $this->assertNotNull($sequence);
        $this->assertSame(2, (int) $sequence->last_number);
    }
}
