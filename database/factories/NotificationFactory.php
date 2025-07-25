<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['order', 'message', 'review', 'system']),
            'content' => $this->faker->sentence(),
            'is_read' => $this->faker->boolean(30),
        ];
    }
}
