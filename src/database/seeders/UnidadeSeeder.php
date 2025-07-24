<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unidades;
use Carbon\Carbon;
class UnidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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
            'nome' => 'Unidade Secundária',
            'status' => 1,
            'id_users_fk' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
