<?php

namespace Modules\Commercial\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Commercial\Models\CommercialDocumentSequence;

class CommercialDocumentSequenceSeeder extends Seeder
{
    /**
     * Seed all supported commercial document sequence types.
     *
     * @return void
     */
    public function run(): void
    {
        foreach (['OPP', 'PROP', 'SO', 'INV', 'RET', 'AR'] as $type) {
            CommercialDocumentSequence::query()->firstOrCreate(
                ['type' => $type],
                ['last_number' => 0]
            );
        }

        $this->command?->info('CommercialDocumentSequenceSeeder completed.');
    }
}

