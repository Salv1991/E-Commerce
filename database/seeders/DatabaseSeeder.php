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
        //Creates users and admin user.
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'user',
            'email' => 'user@user.com',
            'password' => 'user1234',
            'admin' => true,
        ]);
        
        //Creates Products with Images.
        $productsWithImages = ProductImage::factory(50)->create();

        //Creates Categories and children Categories.
        $categories = Category::factory(2)->sequence(
            ['depth' => 0, 'weight' => 0],
            ['depth' => 0, 'weight' => 1],
        )->create();

        $firstDepthCategories = collect();
        foreach ($categories as $category) {
            $newCategories = Category::factory(2)->sequence(
                ['parent_id' => $category->id, 'depth' => 1, 'weight' => 0],
                ['parent_id' => $category->id, 'depth' => 1, 'weight' => 1]
            )->create();

            $firstDepthCategories = $firstDepthCategories->concat($newCategories);
        }

        $childrenCategories = collect();
        foreach ($firstDepthCategories as $firstDepthCategory) {
            $secondDepthCategories = Category::factory(3)->sequence(
                ['parent_id' => $firstDepthCategory->id, 'depth' => 2, 'weight' => 0],
                ['parent_id' => $firstDepthCategory->id, 'depth' => 2, 'weight' => 1],
                ['parent_id' => $firstDepthCategory->id, 'depth' => 2, 'weight' => 2]
            )->create();

            $childrenCategories = $childrenCategories->concat($secondDepthCategories);
        }

        //Attaches categories to each product.
        foreach( $productsWithImages as $productWithImage) {
            $productWithImage->product->categories()->attach($childrenCategories->random());
        }

    }   
}
