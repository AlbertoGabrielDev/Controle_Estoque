<?php

namespace Tests\Unit\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Commercial\Models\CommercialDocumentSequence;
use Modules\Commercial\Models\CommercialOpportunity;
use Modules\Commercial\Services\OpportunityService;
use PHPUnit\Framework\Attributes\Group;
use RuntimeException;
use Tests\TestCase;

#[Group('modularizacao')]
class CommercialOpportunityServiceTest extends TestCase
{
    use RefreshDatabase;

    private OpportunityService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(OpportunityService::class);
        CommercialDocumentSequence::query()->create(['type' => 'OPP', 'last_number' => 0]);
    }

    public function test_create_opportunity_sets_status_novo(): void
    {
        $opp = $this->service->createOpportunity([
            'nome'           => 'Test Opportunity',
            'valor_estimado' => 5000,
        ]);

        $this->assertSame('novo', $opp->status);
        $this->assertSame('OPP-000001', $opp->codigo);
    }

    public function test_mark_as_lost_requires_motivo(): void
    {
        $this->expectException(RuntimeException::class);

        $opp = $this->service->createOpportunity(['nome' => 'Opp X']);
        $this->service->markAsLost($opp->id, '');
    }

    public function test_mark_as_lost_sets_status_perdido(): void
    {
        $opp = $this->service->createOpportunity(['nome' => 'Opp Y']);
        $lost = $this->service->markAsLost($opp->id, 'Preco elevado');

        $this->assertSame('perdido', $lost->status);
        $this->assertSame('Preco elevado', $lost->motivo_perda);
    }

    public function test_mark_as_won_sets_status_ganho(): void
    {
        $opp = $this->service->createOpportunity(['nome' => 'Opp Z']);
        $won = $this->service->markAsWon($opp->id);

        $this->assertSame('ganho', $won->status);
    }

    public function test_cannot_edit_closed_opportunity(): void
    {
        $opp = CommercialOpportunity::query()->create([
            'codigo' => 'OPP-999', 'nome' => 'Closed', 'status' => 'ganho',
            'valor_estimado' => 0,
        ]);

        $this->expectException(RuntimeException::class);
        $this->service->updateOpportunity($opp->id, ['nome' => 'New Name']);
    }
}
