<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CustomerInformation;
use App\Models\User;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $directory = storage_path('app/public/products'); 

        if (File::exists($directory)) {
            // Get all image files
            $files = File::files($directory);

            // Define the images to keep
            $keepFiles = [
                'product1.jpg',
                'product2.jpg',
                'product3.jpg',
                'product5.jpg',
                'product6.jpg',
                'product7.jpg',
                'product8.jpg',
                'product9.jpg',
                'product10.jpg',
                'product11.jpg',
                'product12.jpg',
                'image4.jpg',
                'image5.jpg',
                'image6.jpg',
                'image7.jpg',
                'image8.jpg',
                'image9.jpg'
            ];

            foreach ($files as $file) {
                if (!in_array($file->getFilename(), $keepFiles)) {
                    File::delete($file->getPathname());
                }
            }
        }
        
        //Creates user
        $user = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => 'admin',
            'is_admin' => true,
        ]);
        
        $user->customerInformation()->create([
            'address' => 'Test Street 20',
            'postal_code' => '12345',
            'floor' => '1',
            'country' => 'USA',
            'city' => 'New York',
            'mobile' => '6900000000',
            'alternative_phone' => null,
        ]);

        //Creates 10 users with information.
        CustomerInformation::factory(10)->create();

        //Creates Products with Images.
        $productsWithImages = ProductImage::factory(50)->create();

        //Creates 0 depth Categories.
        $categories = Category::factory(3)->sequence(
            ['depth' => 0, 'weight' => 0],
            ['depth' => 0, 'weight' => 1],
        )->create();

        //Creates 1st depth Categories.
        $firstDepthCategories = collect();
        foreach ($categories as $category) {
            $newCategories = Category::factory(3)->sequence(
                ['parent_id' => $category->id, 'depth' => 1, 'weight' => 0],
                ['parent_id' => $category->id, 'depth' => 1, 'weight' => 1],
                ['parent_id' => $category->id, 'depth' => 1, 'weight' => 2]
            )->create();

            $firstDepthCategories = $firstDepthCategories->concat($newCategories);
        }

        //Creates 2nd depth Categories.
        $childrenCategories = collect();
        foreach ($firstDepthCategories as $firstDepthCategory) {
            $secondDepthCategories = Category::factory(3)->sequence(
                ['parent_id' => $firstDepthCategory->id, 'depth' => 2, 'weight' => 0],
                ['parent_id' => $firstDepthCategory->id, 'depth' => 2, 'weight' => 1],
                ['parent_id' => $firstDepthCategory->id, 'depth' => 2, 'weight' => 2],
            )->create();

            $childrenCategories = $childrenCategories->concat($secondDepthCategories);
        }

        //Attaches 2nd depth categories to each product.
        foreach( $productsWithImages as $productWithImage) {
            $productWithImage->product->categories()->attach($childrenCategories->random());
        }

    }   
}
