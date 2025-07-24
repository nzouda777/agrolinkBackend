<?php

namespace Database\Seeders;

use App\Models\UserFavorite;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        // Créer 100 produits favoris
        UserFavorite::factory(100)->create();
    }
}
