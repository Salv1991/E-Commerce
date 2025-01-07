<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class CustomerInformationFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'address' => fake()->address(),
            'postal_code' => fake()->postcode(),
            'floor' => fake()->numberBetween(1,10),
            'country' => fake()->country(),
            'city' => fake()->city(),
            'mobile' => fake()->phoneNumber(),
            'alternative_phone' => fake()->phoneNumber(),
        ];
    }
}
