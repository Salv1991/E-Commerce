<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryPageTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_category_page_displays_correct_information()
    {
        $category = Category::create([
            'title' => 'Foo',
        ]);

        $category2 = Category::create([
            'title' => 'Bar',
        ]);

        $product1 = Product::create([
            'title' => 'illo',
            'current_price' => 100,   
            'original_price' => 200,
            'discount' => 50,  
        ]);

        $product2 = Product::create([
            'title' => 'yin',
            'current_price' => 500,   
            'original_price' => 100,
            'discount' => 50,  
        ]);

        $product1->categories()->attach($category->id);
        $product2->categories()->attach($category2->id);

        $response = $this->get('categories/' . $category->id);

        $response->assertStatus(200);

        $response->assertSee($category->title);
        $response->assertSee($product1->title);
        $response->assertDontSee($product2->title);
    }

    public function test_category_page_filters()
    {
        $category = Category::create([
            'title' => 'Foo',
        ]);

        $product1 = Product::create([
            'title' => 'illo',
            'current_price' => 100,   
            'original_price' => 200,
            'discount' => 50,  
            'stock' => 2
        ]);

        $product2 = Product::create([
            'title' => 'yin',
            'current_price' => 200,   
            'original_price' => 200,
            'stock' => 2

        ]);

        $product3 = Product::create([
            'title' => 'yang',
            'current_price' => 50,   
            'original_price' => 100,
            'discount' => 50,  
            'stock' => 2

        ]);

        $product1->categories()->attach($category->id);
        $product2->categories()->attach($category->id);
        $product3->categories()->attach($category->id);

        $response = $this->get('categories/' . $category->id . '?price=asc');

        $response->assertStatus(200);
     
        $response->assertSeeInOrder([$product3->title, $product1->title, $product2->title]);

        $response = $this->get('categories/' . $category->id . '?price=desc');

        $response->assertSeeInOrder([$product2->title, $product1->title, $product3->title]);

        $response = $this->get('categories/' . $category->id . '?sort=asc');

        $response->assertSeeInOrder([$product1->title, $product3->title, $product2->title]);

        $response = $this->get('categories/' . $category->id . '?sort=desc');

        $response->assertSeeInOrder([$product2->title, $product3->title, $product1->title]);

        $response = $this->get('categories/' . $category->id . '?discounted_products=1');

        $response->assertDontSee($product2->title);

        $response->assertSee($product1->title);

        $response->assertSee($product3->title);

        $response = $this->get('categories/' . $category->id . '?min_price_range=0&max_price_range=100');

        $response->assertDontSee($product2->title);

        $response->assertSee($product1->title);

        $response->assertSee($product3->title);

        $response = $this->get('categories/' . $category->id . '?min_price_range=101&max_price_range=300');

        $response->assertSee($product2->title);

        $response->assertDontSee($product1->title);

        $response->assertDontSee($product3->title);

    }
}
