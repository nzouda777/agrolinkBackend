<?php

namespace Database\Seeders;

use App\Models\ProductFlag;
use App\Models\UserFlag;
use Illuminate\Database\Seeder;

class FlagSeeder extends Seeder
{
    public function run(): void
    {
        // Créer 30 signalements de produits
        ProductFlag::factory(30)->create();

        // Créer 20 signalements d'utilisateurs
        UserFlag::factory(20)->create();
    }
}
