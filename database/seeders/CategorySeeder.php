<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Créer les catégories principales
        $mainCategories = Category::factory(5)->create();

        // Pour chaque catégorie principale, créer 2-4 sous-catégories
        foreach ($mainCategories as $parent) {
            $subcategories = Category::factory(rand(2, 4))->create([
                'parent_id' => $parent->id,
            ]);

            // Créer des traductions pour chaque catégorie
            foreach ($subcategories as $category) {
                CategoryTranslation::factory(2)->create([
                    'category_id' => $category->id,
                ]);
            }
        }
    }
}
