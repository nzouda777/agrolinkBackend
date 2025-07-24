<?php

namespace Database\Factories;

use App\Models\ShippingAddress;
use App\Models\User;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingAddressFactory extends Factory
{
    protected $model = ShippingAddress::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
            'city_id' => City::factory(),
            'address' => $this->faker->address(),
            'postal_code' => $this->faker->postcode(),
            'is_default' => $this->faker->boolean(30),
        ];
    }
}
