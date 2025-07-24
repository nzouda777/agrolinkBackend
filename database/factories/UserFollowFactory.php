<?php

namespace Database\Factories;

use App\Models\UserFollow;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFollowFactory extends Factory
{
    protected $model = UserFollow::class;

    public function definition(): array
    {
        return [
            'follower_id' => User::factory(),
            'following_id' => User::factory(),
        ];
    }
}
