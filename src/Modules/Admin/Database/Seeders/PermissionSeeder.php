<?php

namespace Modules\Admin\Database\Seeders;

use Modules\Admin\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'view_post'],
            ['name' => 'edit_post'],
            ['name' => 'create_post'],
            ['name' => 'delete_post'],
            ['name' => 'delete_user'],
            ['name' => 'create_user'],
            ['name' => 'export'],
            ['name' => 'import'],
            ['name' => 'status'],
        ];
    
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
