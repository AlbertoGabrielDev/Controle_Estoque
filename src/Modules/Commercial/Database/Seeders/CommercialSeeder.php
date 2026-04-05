<?php

namespace Modules\Commercial\Database\Seeders;

use Illuminate\Database\Seeder;

class CommercialSeeder extends Seeder
{
    /**
     * Run all default seeders for the Commercial module.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            CommercialDocumentSequenceSeeder::class,
            CommercialDiscountPolicySeeder::class,
            CommercialFlowDemoSeeder::class,
        ]);

        $this->command?->info('CommercialSeeder completed.');
    }
}

