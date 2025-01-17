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
            'products/image4.jpg',
            'products/image5.jpg',
            'products/image6.jpg',
            'products/image7.jpg',
            'products/image8.jpg',
            'products/image9.jpg',
            'products/image10.jpg',
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
