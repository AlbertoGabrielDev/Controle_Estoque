<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UnidadeSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(PermissionSeeder::class);
        \App\Models\Categoria::factory(25)->create();
        \App\Models\Produto::factory(80)->create();


    }
}
