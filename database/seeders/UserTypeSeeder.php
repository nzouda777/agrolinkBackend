<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Seeders\Seeder;

class UserTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les types d'utilisateurs par défaut
        $types = [
            ['name' => 'individual', 'description' => 'Utilisateur individuel'],
            ['name' => 'business', 'description' => 'Entreprise'],
        ];

        foreach ($types as $type) {
            UserType::create($type);
        }
    }
}
