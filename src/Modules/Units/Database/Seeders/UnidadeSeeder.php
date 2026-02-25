<?php

namespace Modules\Units\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\Units\Models\Unidades;

class UnidadeSeeder extends Seeder
{
    public function run(): void
    {
        Unidades::create([
            'nome' => 'Unidade Central',
            'status' => 1,
            'id_users_fk' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Unidades::create([
            'nome' => 'Unidade Secundaria',
            'status' => 1,
            'id_users_fk' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
