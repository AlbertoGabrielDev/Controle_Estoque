<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'  => 'Admin',
                'email' => 'admin@hotmail.com',
                'password' => '123456789',
                'status' => 1,
            ],
            [
                'name'  => 'Vendedor',
                'email' => 'vendedor@hotmail.com',
                'password' => '123456789',
                'status' => 1,
            ],
            [
                'name'  => 'Vendedor2',
                'email' => 'vendedo2r@hotmail.com', // (mantido como você enviou)
                'password' => '123456789',
                'status' => 1,
            ],
            [
                'name'  => 'Marketing',
                'email' => 'marketing@hotmail.com',
                'password' => '123456789',
                'status' => 1,
            ],
        ];

        foreach ($users as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']], // critério de existência
                [
                    'name' => $u['name'],
                    'password' => Hash::make($u['password']),
                    'status' => $u['status'],
                    'profile_photo_path' => null,
                ]
            );

            $this->command?->info(
                $user->wasRecentlyCreated
                    ? "Criado: {$u['email']}"
                    : "Já existia: {$u['email']} (não alterado)"
            );
        }
    }
}
