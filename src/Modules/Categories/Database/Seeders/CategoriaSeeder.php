<?php

namespace Modules\Categories\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Categories\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        Categoria::factory(25)->create();
    }
}
