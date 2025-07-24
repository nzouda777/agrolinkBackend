<?php

namespace Database\Factories;

use App\Models\UserFlag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFlagFactory extends Factory
{
    protected $model = UserFlag::class;

    public function definition(): array
    {
        return [
            'reporter_id' => User::factory(),
            'reported_id' => User::factory(),
            'reason' => $this->faker->randomElement(['fake', 'spam', 'offensive', 'other']),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'resolved', 'rejected']),
        ];
    }
}
