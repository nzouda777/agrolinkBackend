<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'seller_id' => User::factory(),
            'category_id' => Category::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'old_price' => $this->faker->optional(0.5)->randomFloat(2, 10, 2000),
            'unit' => $this->faker->randomElement(['kg', 'g', 'l', 'unit']),
            'quantity' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement(['active', 'inactive', 'pending', 'rejected']),
            'featured' => $this->faker->boolean(20),
        ];
    }
}
