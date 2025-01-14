<?php

namespace Tests\Feature;

use App\Models\LineItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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

    public function test_cart_order_summary_for_guest_user() {
        $product = Product::create(['title' => 'bar', 'current_price' => 20, 'stock' => 22]);
        $product2 = Product::create(['title' => 'foobar', 'current_price' => 10, 'stock' => 12]);
        
        $this->withSession([
            'guest' => [
                'cart' => [
                    $product->id => [
                        'product_id' => $product->id,
                        'quantity' => 3,
                        'price' => $product->current_price,
                    ]
                ],
                'shipping_method' => [
                    'value' => 'elta',
                    'extra_cost' => 3.40,   
                ],
                'payment_method' => [
                    'value' => '',
                    'extra_cost' => 0,   
                ],
            ]
        ]);
      
        $response = $this->get(route('cart'));

        $response->assertStatus(200);
        
        $response->assertSee($product->title);

        $response->assertDontSee($product2->title);

        $response->assertSeeHtml('<span class="cart-subtotal">60.00$</span>');
        $response->assertSeeHtml('<span class="cart-total font-bold">63.40$</span>');
        
        $session = session()->get('guest');

        $session['cart'][$product2->id] = [
            'product_id' => $product2->id,
            'quantity' => 2,
            'price' => $product2->current_price,   
        ]; 

        $session['payment_method'] = [
            'value' => 'bank_transfer',
            'extra_cost' => 0,   
        ];
    
        session()->put('guest', $session);

        $response = $this->get(route('cart'));
    
        $response->assertSeeHtml('<span id="shipping-fee">3.40$</span>');
        $response->assertSeeHtml('<span id="payment-fee">2.00$</span>');
        $response->assertSeeHtml('<span class="cart-subtotal">80.00$</span>');
        $response->assertSeeHtml('<span class="cart-total font-bold">85.40$</span>');
        
    }

    public function test_cart_merge_for_users_that_login() {
        $user2 = User::create([
            'name' => 'Foobar2',    
            'email' => 'test1234@example.com',
            'password' => '12341312'
        ]);   

        $product1 = Product::create(['title' => 'bar', 'current_price' => 20, 'stock' => 22]);
        $product2 = Product::create(['title' => 'foobar', 'current_price' => 10, 'stock' => 12]);
        
        $this->withSession([
            'guest' => [
                'cart' => [
                    $product1->id => [
                        'product_id' => $product1->id,
                        'quantity' => 3,
                        'price' => $product1->current_price,
                    ]
                ],
            ]
        ]);

        $response = $this->post(route('login'), [
            'email' => $user2->email,
            'password' => '12341312',
        ]);      

        $response->assertRedirect('/');


        $response = $this->get(route('cart'));

        $response->assertStatus(200);
        
        $response->assertSee($product1->title);

        $response->assertDontSee($product2->title);

        $response->assertSeeHtml('<span class="cart-subtotal">60.00$</span>');
        $response->assertSeeHtml('<span class="cart-total font-bold">63.40$</span>');
    }

    public function test_cart_merge_for_users_that_signup() {           
        $product1 = Product::create(['title' => 'bar', 'current_price' => 20, 'stock' => 22]);
        $product2 = Product::create(['title' => 'foobar', 'current_price' => 10, 'stock' => 12]);
        
        $this->withSession([
            'guest' => [
                'cart' => [
                    $product1->id => [
                        'product_id' => $product1->id,
                        'quantity' => 3,
                        'price' => $product1->current_price,
                    ]
                ],
                'shipping_method' => [
                    'value' => 'elta',
                    'extra_cost' => 3.40,   
                ],
                'payment_method' => [
                    'value' => '',
                    'extra_cost' => 0,   
                ],
            ]
        ]);

        $response = $this->post(route('signup'), [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => '11111111',
            'password_confirmation' => '11111111',
        ]);      
    
        $response->assertRedirect('/');

        $response = $this->get(route('cart'));

        $response->assertStatus(200);
        
        $response->assertSee($product1->title);

        $response->assertDontSee($product2->title);

        $response->assertSeeHtml('<span class="cart-subtotal">60.00$</span>');
        $response->assertSeeHtml('<span class="cart-total font-bold">63.40$</span>');    
    }

    public function test_cart_merge_when_quantity_exceeds_product_stock() {
        $user = User::create([
            'name' => 'Foobar2',    
            'email' => 'test1234@example.com',
            'password' => '12341312'
        ]);   
             
        $product1 = Product::create(['title' => 'bar', 'current_price' => 20, 'stock' => 2]);
        $product2 = Product::create(['title' => 'foobar', 'current_price' => 10, 'stock' => 4]);
        
        $this->withSession([
            'guest' => [
                'cart' => [
                    $product1->id => [
                        'product_id' => $product1->id,
                        'quantity' => 2,
                        'price' => $product1->current_price,
                    ],
                    $product2->id => [
                        'product_id' => $product2->id,
                        'quantity' => 5,
                        'price' => $product2->current_price,
                    ]
                ],
            ]
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => '12341312',
        ]);      

        $response->assertRedirect('/');

        $response = $this->get(route('cart'));

        $response->assertStatus(200);
        
        $response->assertSee($product1->title);

        $response->assertSee($product2->title);

        $response->assertSeeHtml('<span class="cart-subtotal">80.00$</span>');
        $response->assertSeeHtml('<span class="cart-total font-bold">83.40$</span>');
        
       
        
    }

    public function test_cart_merge_when_quantity_exceeds_product_stock_on_existing_order() {
        $user = User::create([
            'name' => 'Foobar2',    
            'email' => 'test1234@example.com',
            'password' => '12341312'
        ]);   

        $product1 = Product::create(['title' => 'bar', 'current_price' => 20, 'stock' => 3]);
        $product2 = Product::create(['title' => 'foobar', 'current_price' => 10, 'stock' => 5]);
        
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total_price' => 45.4,
            'subtotal' => 40,
            'payment_method' => 'bank_transfer',
            'payment_fee' => 2,
            'shipping_method' => 'elta',
            'shipping_fee' => 3.40,
        ]);

        LineItem::create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'price' => 20, 
        ]);
        
        $this->withSession([
            'guest' => [
                'cart' => [
                    $product1->id => [
                        'product_id' => $product1->id,
                        'quantity' => 2,
                        'price' => $product1->current_price,
                    ],
                    $product2->id => [
                        'product_id' => $product2->id,
                        'quantity' => 5,
                        'price' => $product2->current_price,
                    ]
                ],
            ]
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => '12341312',
        ]);      

        $response->assertRedirect('/');

        $response = $this->get(route('cart'));

        $response->assertStatus(200);
        
        $response->assertSee($product1->title);

        $response->assertSee($product2->title);

        $response->assertSeeHtml('<span class="cart-subtotal">110.00$</span>');
        $response->assertSeeHtml('<span class="cart-total font-bold">115.40$</span>');
    }
}
