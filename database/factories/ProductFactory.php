<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->word(),
            'mpn' => fake()->shuffleString(),
            'description' => fake()->realText(),
            'price' => fake()->numberBetween(1, 999),
            'discounted_price' => null,
            'stock' => fake()->numberBetween(0,20),
        ];
    }
}
