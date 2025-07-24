<?php

namespace Database\Factories;

use App\Models\UserReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserReportFactory extends Factory
{
    protected $model = UserReport::class;

    public function definition(): array
    {
        return [
            'reporter_id' => User::factory(),
            'reported_id' => User::factory(),
            'type' => $this->faker->randomElement(['fraud', 'spam', 'harassment', 'other']),
            'details' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'resolved', 'rejected']),
        ];
    }
}
