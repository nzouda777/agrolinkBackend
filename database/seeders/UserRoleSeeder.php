<?php

namespace Database\Seeders;

use App\Models\UserRole;
use Illuminate\Database\Seeders\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les rôles par défaut
        $roles = [
            ['name' => 'admin', 'description' => 'Administrateur du système'],
            ['name' => 'seller', 'description' => 'Vendeur'],
            ['name' => 'customer', 'description' => 'Client'],
        ];

        foreach ($roles as $role) {
            UserRole::create($role);
        }
    }
}
