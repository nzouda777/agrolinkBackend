<?php

namespace Database\Factories;

use App\Models\DeliveryRate;
use App\Models\DeliveryOption;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryRateFactory extends Factory
{
    protected $model = DeliveryRate::class;

    public function definition(): array
    {
        return [
            'delivery_option_id' => DeliveryOption::factory(),
            'city_id' => City::factory(),
            'base_price' => $this->faker->randomFloat(2, 5, 50),
            'per_km_price' => $this->faker->randomFloat(2, 0.5, 2),
        ];
    }
}
