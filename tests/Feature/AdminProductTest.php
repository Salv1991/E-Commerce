<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_product_edit_changes_are_saved_successfully() {
        $user = User::create([
            'name' => 'Michael Scott',    
            'email' => 'michaelScott@example.com',
            'password' => '12341312',
            'is_admin' => true
        ]);

        $this->actingAs($user);

        $product = Product::create([
            'title' => 'foo',
            'current_price' => 150, 
            'original_price' => 150,
            'stock' => 10  
        ]);

        $response = $this->get(route('admin.product.show', $product->id));

        $response->assertStatus(200);
        
        $response->assertSee($product->title);
        
        $this->patch(route('admin.product.edit',  ['id' => $product->id]),[
            'title' => 'bar',
            'current_price' => 50,
            'original_price' => 100,
            'description' => 'Description text.',
            'stock' => 5,
            'mpn' => '23134fsa231'
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('products', ([
            'mpn' => '23134fsa231',
            'title' => 'bar',
            'current_price' => 50,
            'original_price' => 100,
            'description' => 'Description text.',
            'stock' => 5
        ])
        );
    }

    public function test_product_try_to_edit_changes_with_empty_fields() {
        $user = User::create([
            'name' => 'Michael Scott',    
            'email' => 'michaelScott@example.com',
            'password' => '12341312',
            'is_admin' => true
        ]);

        $this->actingAs($user);

        $product = Product::create([
            'title' => 'foo',
            'mpn' => '123',
            'current_price' => 150, 
            'original_price' => 150,
            'stock' => 10  
        ]);

        $response = $this->get(route('admin.product.show', $product->id));

        $response->assertStatus(200);
        
        $response->assertSee($product->title);
        
        $this->patch(route('admin.product.edit',  ['id' => $product->id]),[
            'title' => '',
            'current_price' => '',
            'original_price' => null,
            'description' => '',
            'stock' => null,
            'mpn' => 'wafwaaw2dw22' 
        ]);

        $response->assertSessionHasErrors(['title', 'stock', 'description', 'original_price', 'current_price']);
        
        $this->assertDatabaseHas('products', ([
                'title' => $product->title,
                'current_price' => $product->current_price,
                'original_price' => $product->original_price,
                'description' => $product->description,
                'stock' => $product->stock
            ])
        );
    }

    public function test_product_current_price_greater_than_original_price() {
        $user = User::create([
            'name' => 'Michael Scott',    
            'email' => 'michaelScott@example.com',
            'password' => '12341312',
            'is_admin' => true
        ]);

        $this->actingAs($user);

        $product = Product::create([
            'title' => 'foo',
            'current_price' => 150, 
            'original_price' => 150,
            'stock' => 10  
        ]);

        $response = $this->get(route('admin.product.show', $product->id));

        $response->assertStatus(200);
        
        $response->assertSee($product->title);
        
        $this->patch(route('admin.product.edit',  ['id' => $product->id]),[
            'title' => 'bar',
            'current_price' => 101,
            'original_price' => 100,
            'description' => 'Description text.',
            'stock' => 5,
            'mpn' => 'wafwaaw2dw22'
        ]);
          
        $response->assertSessionHasErrors(['original_price']);

        $this->assertDatabaseHas('products', ([
                'mpn' => $product->mpn, 
                'title' => $product->title,
                'current_price' => $product->current_price,
                'original_price' => $product->original_price,
                'description' => $product->description,
                'stock' => $product->stock
            ])
        );
    }
}
