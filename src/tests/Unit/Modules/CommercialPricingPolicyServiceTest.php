<?php

namespace Tests\Unit\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Commercial\Models\CommercialDiscountPolicy;
use Modules\Commercial\Services\PricingPolicyService;
use PHPUnit\Framework\Attributes\Group;
use RuntimeException;
use Tests\TestCase;

#[Group('modularizacao')]
class CommercialPricingPolicyServiceTest extends TestCase
{
    use RefreshDatabase;

    private PricingPolicyService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PricingPolicyService::class);
    }

    public function test_validate_discount_passes_within_limit(): void
    {
        CommercialDiscountPolicy::query()->create([
            'nome' => 'Policy Test',
            'tipo' => 'item',
            'percentual_maximo' => 20.00,
            'ativo' => true,
        ]);

        // Should not throw
        $this->service->validateDiscount(15.0, 'item');
        $this->assertTrue(true);
    }

    public function test_validate_discount_throws_when_exceeds_limit(): void
    {
        CommercialDiscountPolicy::query()->create([
            'nome' => 'Policy Test',
            'tipo' => 'item',
            'percentual_maximo' => 10.00,
            'ativo' => true,
        ]);

        $this->expectException(RuntimeException::class);
        $this->service->validateDiscount(15.0, 'item');
    }

    public function test_apply_order_discount_computes_amount(): void
    {
        CommercialDiscountPolicy::query()->create([
            'nome' => 'Pedido Policy',
            'tipo' => 'pedido',
            'percentual_maximo' => 30.00,
            'ativo' => true,
        ]);

        $amount = $this->service->applyOrderDiscount(1000.0, 10.0);
        $this->assertSame(100.0, $amount);
    }

    public function test_validate_discount_passes_when_no_policy_configured(): void
    {
        // No policy in DB — should not throw
        $this->service->validateDiscount(99.0, 'item');
        $this->assertTrue(true);
    }
}
