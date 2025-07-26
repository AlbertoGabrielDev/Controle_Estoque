<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\UserRole;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@hotmail.com',
            'password' => Hash::make('123456789'),
            'status' => 1,
            'profile_photo_path' => null,
        ]);

    }
}
