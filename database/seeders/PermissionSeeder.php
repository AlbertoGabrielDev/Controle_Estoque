<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'view_post']);
        Permission::create(['name' => 'edit_post']); 
        Permission::create(['name' => 'create_post']);
        Permission::create(['name' => 'create_user']);  
        Permission::create(['name' => 'delete_user']);
    }
}
