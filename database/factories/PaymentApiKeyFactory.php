<?php

namespace Database\Factories;

use App\Models\PaymentApiKey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentApiKeyFactory extends Factory
{
    protected $model = PaymentApiKey::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'provider' => $this->faker->company(),
            'api_key' => $this->faker->uuid(),
            'api_secret' => $this->faker->password(),
            'is_live' => $this->faker->boolean(20),
        ];
    }
}
