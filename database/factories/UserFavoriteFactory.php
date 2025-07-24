<?php

namespace Database\Factories;

use App\Models\UserFavorite;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFavoriteFactory extends Factory
{
    protected $model = UserFavorite::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
        ];
    }
}
