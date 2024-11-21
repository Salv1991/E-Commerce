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
        $price = fake()->numberBetween(1, 999);
        $discount = fake()->boolean(50) ? fake()->numberBetween(5, 70) : null;
        return [
            'title' => fake()->word(),
            'mpn' => fake()->shuffleString(),
            'description' => fake()->text(400),
            'current_price' => $price - $price * ($discount/100 ?? 0),
            'original_price' => $price,
            'discount' => $discount,
            'stock' => fake()->numberBetween(0,20),
        ];
    }
}
