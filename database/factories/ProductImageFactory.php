<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $images = [
            'products/product1.jpg',
            'products/product2.jpg',
            'products/product3.jpg',
            'products/product4.jpg',
            'products/product5.jpg',
            'products/product6.jpg',
            'products/product7.jpg',
            'products/product8.jpg',
        ];

        return [
            'product_id' => Product::factory(),
            'image_path' => $this->faker->randomElement($images),
        ];
    }
}
