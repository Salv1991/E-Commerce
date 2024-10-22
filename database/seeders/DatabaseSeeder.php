<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'user',
            'email' => 'user@user.com',
            'password' => 'user1234',
            'admin' => true,
        ]);
        
        $categories = Category::factory(5)->create();

        $productImages = ProductImage::factory(50)->create();

        foreach( $productImages as $productImage) {
            $productImage->product->categories()->attach($categories->random());
        }

    }   
}
