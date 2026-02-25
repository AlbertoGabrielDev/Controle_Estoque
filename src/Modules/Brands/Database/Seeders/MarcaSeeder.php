<?php

namespace Modules\Brands\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Brands\Models\Marca;

class MarcaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Marca::factory(20)->create();
    }
}
