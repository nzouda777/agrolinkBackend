<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un utilisateur admin
        User::factory()->create([
            'email' => 'admin@agrolink.com',
            'password' => Hash::make('admin123'),
            'role_id' => 1, // 1 est l'ID du rôle admin
            'status' => 'active',
            'email_verified' => true,
            'phone_verified' => true,
        ]);

        // Créer 20 utilisateurs normaux
        User::factory(20)->create();
    }
}
