<?php

namespace Database\Seeders;

use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Database\Seeder;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        // Créer 20 listes de souhaits
        $wishlists = Wishlist::factory(20)->create();

        // Pour chaque liste de souhaits, ajouter 1-5 produits
        foreach ($wishlists as $wishlist) {
            WishlistItem::factory(rand(1, 5))->create([
                'wishlist_id' => $wishlist->id,
            ]);
        }
    }
}
