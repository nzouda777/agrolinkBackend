<?php

namespace Database\Factories;

use App\Models\ProductRating;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductRatingFactory extends Factory
{
    protected $model = ProductRating::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
        ];
    }
}
