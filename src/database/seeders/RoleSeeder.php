<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'gerente']);
        Role::create(['name' => 'atendente']);
        Role::create(['name' => 'marketing']);


        UserRole::create([
            'user_id' => 1,
            'role_id' => 1,
        ]);
    }
}
