<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['mobile_money', 'bank_transfer', 'card', 'cash']),
            'provider' => $this->faker->company(),
            'account_number' => $this->faker->numerify('########'),
            'is_default' => $this->faker->boolean(30),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
