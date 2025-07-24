<?php

namespace Database\Factories;

use App\Models\WishlistItem;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class WishlistItemFactory extends Factory
{
    protected $model = WishlistItem::class;

    public function definition(): array
    {
        return [
            'wishlist_id' => Wishlist::factory(),
            'product_id' => Product::factory(),
        ];
    }
}
