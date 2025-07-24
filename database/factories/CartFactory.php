<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'subtotal' => $this->faker->randomFloat(2, 10, 1000),
            'shipping_cost' => $this->faker->randomFloat(2, 5, 50),
            'tax' => $this->faker->randomFloat(2, 1, 50),
            'total' => $this->faker->randomFloat(2, 15, 1500),
        ];
    }
}
