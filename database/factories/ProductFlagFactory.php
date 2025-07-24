<?php

namespace Database\Factories;

use App\Models\ProductFlag;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFlagFactory extends Factory
{
    protected $model = ProductFlag::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'reason' => $this->faker->randomElement(['fake', 'spam', 'offensive', 'other']),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'resolved', 'rejected']),
        ];
    }
}
