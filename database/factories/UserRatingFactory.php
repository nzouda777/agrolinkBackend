<?php

namespace Database\Factories;

use App\Models\UserRating;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserRatingFactory extends Factory
{
    protected $model = UserRating::class;

    public function definition(): array
    {
        return [
            'rater_id' => User::factory(),
            'rated_id' => User::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->paragraph(),
        ];
    }
}
