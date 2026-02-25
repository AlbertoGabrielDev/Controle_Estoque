<?php

namespace Modules\Admin\Database\Seeders;

use Modules\Admin\Models\Role;
use Modules\Admin\Models\User;
use Modules\Admin\Models\UserRole;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Cria os roles apenas se não existirem
        $names = ['admin', 'gerente', 'atendente', 'marketing'];
        $ids = [];

        foreach ($names as $name) {
            $role = Role::firstOrCreate(['name' => $name]);
            $ids[$name] = $role->id;

            $this->command?->info(
                $role->wasRecentlyCreated ? "Criado role: {$name}" : "Role já existia: {$name}"
            );
        }

        // Vincula o usuário 1 ao role admin, se existir
        $userId = 1;
        if (User::whereKey($userId)->exists()) {
            UserRole::firstOrCreate([
                'user_id' => $userId,
                'role_id' => $ids['admin'] ?? null,
            ]);

            $this->command?->info("Vínculo user_id={$userId} -> role=admin garantido (sem duplicar).");
        } else {
            $this->command?->warn("Usuário id={$userId} não existe — vínculo não criado.");
        }
    }
}
