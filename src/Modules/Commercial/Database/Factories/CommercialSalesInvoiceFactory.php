<?php

namespace Modules\Commercial\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Commercial\Models\CommercialSalesInvoice;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Commercial\Models\CommercialSalesInvoice>
 */
class CommercialSalesInvoiceFactory extends Factory
{
    protected $model = CommercialSalesInvoice::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 50, 5000);

        return [
            'numero'          => 'INV-' . $this->faker->unique()->numberBetween(1, 999999),
            'order_id'        => null,
            'cliente_id'      => null,
            'status'          => 'emitida',
            'data_emissao'    => now()->format('Y-m-d'),
            'data_vencimento' => now()->addDays(30)->format('Y-m-d'),
            'observacoes'     => null,
            'subtotal'        => $subtotal,
            'desconto_total'  => 0,
            'total_impostos'  => 0,
            'total'           => $subtotal,
        ];
    }

    public function cancelada(): static
    {
        return $this->state(['status' => 'cancelada']);
    }
}
