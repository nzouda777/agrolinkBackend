<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Créer 50 produits
        $products = Product::factory(50)->create();

        // Pour chaque produit, créer 1-3 images
        foreach ($products as $product) {
            ProductImage::factory(rand(1, 3))->create([
                'product_id' => $product->id,
            ]);

            // Créer 1-2 variantes pour chaque produit
            ProductVariant::factory(rand(1, 2))->create([
                'product_id' => $product->id,
            ]);
        }
    }
}
