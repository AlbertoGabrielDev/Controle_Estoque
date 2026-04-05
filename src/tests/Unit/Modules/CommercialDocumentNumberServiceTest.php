<?php

namespace Tests\Unit\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Modules\Commercial\Models\CommercialDocumentSequence;
use Modules\Commercial\Services\CommercialDocumentNumberService;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('modularizacao')]
class CommercialDocumentNumberServiceTest extends TestCase
{
    use RefreshDatabase;

    private CommercialDocumentNumberService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CommercialDocumentNumberService::class);
    }

    public function test_generate_increments_and_formats_sequence(): void
    {
        $first  = $this->service->generate('SO');
        $second = $this->service->generate('SO');

        $this->assertSame('SO-000001', $first);
        $this->assertSame('SO-000002', $second);

        $seq = CommercialDocumentSequence::query()->where('type', 'SO')->first();
        $this->assertNotNull($seq);
        $this->assertSame(2, (int) $seq->last_number);
    }

    public function test_different_types_have_independent_sequences(): void
    {
        $opp  = $this->service->generate('OPP');
        $prop = $this->service->generate('PROP');

        $this->assertSame('OPP-000001', $opp);
        $this->assertSame('PROP-000001', $prop);
    }

    public function test_throws_for_invalid_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service->generate('INVALID');
    }
}
