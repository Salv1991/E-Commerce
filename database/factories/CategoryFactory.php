<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {   
        $images = [
            'products/image1.jpg',
            'products/image2.jpg',
            'products/image3.jpg',
            'products/image4.jpg',
        ];

        $title = fake()->unique()->word(); 
        $slug = Str::slug($title);
        
        return [
            'slug' => $slug,
            'title' => $title,
            'description' => fake()->text(),
            'image_path' => $this->faker->randomElement($images),
        ];
    }
}
