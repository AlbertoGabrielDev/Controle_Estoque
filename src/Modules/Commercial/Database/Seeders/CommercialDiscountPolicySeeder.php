<?php

namespace Modules\Commercial\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Commercial\Models\CommercialDiscountPolicy;

class CommercialDiscountPolicySeeder extends Seeder
{
    /**
     * Seed baseline discount policies for item and order documents.
     *
     * @return void
     */
    public function run(): void
    {
        CommercialDiscountPolicy::query()->firstOrCreate(
            ['nome' => 'Padrao Item'],
            ['tipo' => 'item', 'percentual_maximo' => 15, 'ativo' => true]
        );

        CommercialDiscountPolicy::query()->firstOrCreate(
            ['nome' => 'Padrao Pedido'],
            ['tipo' => 'pedido', 'percentual_maximo' => 10, 'ativo' => true]
        );

        $this->command?->info('CommercialDiscountPolicySeeder completed.');
    }
}

