<?php

namespace Tests\Feature;

use App\Models\LineItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_cart_page_free_shipping_fee() {
        $user = User::create([
            'name' => 'Foobar',    
            'email' => 'test123@example.com',
            'password' => '12341312'
        ]);

        $this->actingAs($user);

        $product = Product::create([
            'title' => 'foo',
            'current_price' => 150,    
        ]);

        $product2 = Product::create([
            'title' => 'bar',
            'current_price' => 50,    
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total_price' => 0,
            'subtotal' => 0,
            'payment_method' => null,
            'payment_fee' => 0,
            'shipping_method' => 'elta',
            'shipping_fee' => 3.40,
        ]);

        $lineItem1 = LineItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 150,
        ]);

        $lineItem2 = LineItem::create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => 50,
        ]);

        $lineItem1->refresh();
        
        $response = $this->get(route('cart'));

        $response->assertStatus(200);
        
        $response->assertSee($product->title);

        $response->assertSee($product2->title);

        $response->assertSeeHtml('<span id="shipping-fee">Free</span>');
        
        $lineItem1->update(['quantity' => 1]);

        $lineItem1->refresh();

        $response->assertSeeHtml('<span id="shipping-fee">Free</span>');

        $response = $this->get(route('cart'));

        $lineItem1->update(['quantity' => 0]);

        $lineItem1->refresh();

        $response = $this->get(route('cart'));
        
        $response->assertSeeHtml('<span id="shipping-fee">3.40$</span>');
    }

}
