<?php

namespace Database\Seeders;

use App\Models\Unidades;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unidades::create(['nome' => 'Sede', 'status' => 1, 'id_users_fk' => 1]);
    }
}
