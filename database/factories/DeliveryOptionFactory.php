<?php

namespace Database\Factories;

use App\Models\DeliveryOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryOptionFactory extends Factory
{
    protected $model = DeliveryOption::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'is_default' => $this->faker->boolean(30),
        ];
    }
}
