<?php

namespace Database\Factories;

use App\Models\ProductReport;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductReportFactory extends Factory
{
    protected $model = ProductReport::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'type' => $this->faker->randomElement(['fraud', 'spam', 'harassment', 'other']),
            'details' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'resolved', 'rejected']),
        ];
    }
}
