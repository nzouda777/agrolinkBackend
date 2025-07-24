<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(),
            'verified_purchase' => $this->faker->boolean(70),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
