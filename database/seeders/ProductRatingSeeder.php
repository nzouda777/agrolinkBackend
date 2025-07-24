<?php

namespace Database\Seeders;

use App\Models\ProductRating;
use Illuminate\Database\Seeder;

class ProductRatingSeeder extends Seeder
{
    public function run(): void
    {
        // Créer 200 évaluations produits
        ProductRating::factory(200)->create();
    }
}
