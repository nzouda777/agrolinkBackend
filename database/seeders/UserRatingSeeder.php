<?php

namespace Database\Seeders;

use App\Models\UserRating;
use Illuminate\Database\Seeder;

class UserRatingSeeder extends Seeder
{
    public function run(): void
    {
        // Créer 150 évaluations utilisateurs
        UserRating::factory(150)->create();
    }
}
