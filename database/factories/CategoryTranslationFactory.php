<?php

namespace Database\Factories;

use App\Models\CategoryTranslation;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryTranslationFactory extends Factory
{
    protected $model = CategoryTranslation::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'locale' => $this->faker->randomElement(['fr', 'en', 'es']),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
        ];
    }
}
