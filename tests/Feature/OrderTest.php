<?php

namespace Tests\Feature;

use App\Models\LineItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;


class OrderTest extends TestCase
{
    use DatabaseTransactions;

    public function test_cart_page_order_summary_for_authenticated_user() {

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
        $lineItem2->refresh();
        $order->refresh();

        $this->assertEquals(350, $order->total_price);
        $this->assertEquals(350, $order->subtotal);
        $this->assertEquals(0, $order->shipping_fee);
        $this->assertEquals(0, $order->payment_fee);

        $lineItem1->update(['quantity' => 0]);
        $lineItem2->update(['quantity' => 2]);

        $order->refresh();

        $this->assertEquals(103.4, $order->total_price);
        $this->assertEquals(100, $order->subtotal);
        $this->assertEquals(3.40, $order->shipping_fee);
        $this->assertEquals(0, $order->payment_fee);

        $order->update(['payment_method' => 'credit_card', 'payment_fee' => 5.00]);

        $user->currentOrder()->first()->calculateSubtotal();

        $order->refresh();

        $this->assertEquals(108.4, $order->total_price);
        $this->assertEquals(100, $order->subtotal);
        $this->assertEquals(3.40, $order->shipping_fee);
        $this->assertEquals(5, $order->payment_fee);
    }

    public function test_complete_order_successfully() {
        $user = User::create([
            'name' => 'Test Name',    
            'email' => 'test1234@example.com',
            'password' => '12341312'
        ]);   

        $this->actingAs($user);

        $product1 = Product::create(['title' => 'bar', 'current_price' => 20, 'stock' => 3]);
        $product2 = Product::create(['title' => 'foobar', 'current_price' => 10, 'stock' => 5]);
        
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total_price' => 85.4,
            'subtotal' => 80,
            'payment_method' => 'bank_transfer',
            'payment_fee' => 2.00,
            'shipping_method' => 'elta',
            'shipping_fee' => 3.40,
        ]);

        $lineItem1 = LineItem::create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'price' => 20, 
        ]);
        
        $lineItem2 = LineItem::create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'quantity' => 4,
            'price' => 10, 
        ]);

        $response = $this->get(route('cart'));

        $response->assertStatus(200);

        $response = $this->get(route('checkout.login'));

        $response->assertRedirect(route('checkout.customer'));

        $response->assertStatus(302);

        $response = $this->get(route('checkout.order'));

        $response->assertStatus(200);
        
        $response = $this->post(route('checkout.order.complete'), [
            'payment_method' => 'bank_transfer',
            'shipping_method' => 'elta'    
        ]);

        $response->assertRedirect(route('order.success'));
        
        $this->assertEquals($order->refresh()->status, 'completed');
    }

    public function test_complete_order_with_nonexistent_payment_and_shipping_methods() {
        $user = User::create([
            'name' => 'Test Name',    
            'email' => 'test1234@example.com',
            'password' => '12341312'
        ]);   

        $this->actingAs($user);

        $product1 = Product::create(['title' => 'bar', 'current_price' => 20, 'stock' => 3]);
        $product2 = Product::create(['title' => 'foobar', 'current_price' => 10, 'stock' => 5]);
        
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total_price' => 85.4,
            'subtotal' => 80,
            'payment_method' => 'bank_transfer',
            'payment_fee' => 2.00,
            'shipping_method' => 'elta',
            'shipping_fee' => 3.40,
        ]);

        $lineItem1 = LineItem::create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'price' => 20, 
        ]);
        
        $lineItem2 = LineItem::create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'quantity' => 4,
            'price' => 10, 
        ]);

        $response = $this->get(route('cart'));

        $response->assertStatus(200);

        $response = $this->get(route('checkout.login'));

        $response->assertRedirect(route('checkout.customer'));

        $response->assertStatus(302);

        $response = $this->get(route('checkout.order'));

        $response->assertStatus(200);
        
        $response = $this->post(route('checkout.order.complete'), [
            'payment_method' => 'test_method',
            'shipping_method' => 'test_method_2'    
        ]);

        $response->assertRedirect(route('checkout.order'));

        $response->assertSessionHasErrors(['payment_method', 'shipping_method']);
    }

    public function test_order_payment_method_with_additional_fees()
    {
        $product = Product::create(['current_price' => 100, 'stock' => 22]);
        
        $user = User::create(['name' => 'test', 'email' => 'test@test.com', 'password' => '123123123']);
        
        $this->actingAs($user);    
        
        $order = Order::create([
            'user_id' => $user->id,  
            'status' => 'pending', 
            'subtotal' => 100,
            'total_price' => 108.40,
            'payment_method' => 'credit_card',
            'payment_fee' => 5,
            'shipping_method' => 'elta',
            'shipping_fee' => 3.40,
        ]);

        $lineItem = LineItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100,
        ]);

        $order->update([
            'shipping_method' => 'usps',
            'shipping_fee' => 10.00,
            'payment_method' => 'paypal',
            'payment_fee' => 7.50
        ]);

        $order->calculateSubtotal();

        $order->refresh();

        $this->post(route('checkout.order.complete'), [
            'payment_method' => 'paypal', 
            'shipping_method' => 'elta',
        ]);

        $this->assertEquals(117.5, $order->total_price); 
    }

    public function test_cart_page_order_summary_for_guest_user() {
  
        $product = Product::create(['title' => 'foo', 'current_price' => 150]);

        $product2 = Product::create(['title' => 'bar', 'current_price' => 50]);

        $this->withSession([
            'guest' => [
                'cart' => [
                    $product->id => [
                        'product_id' => $product->id,
                        'quantity' => 3,
                        'price' => $product->current_price,
                    ],
                    $product2->id => [
                        'product_id' => $product2->id,
                        'quantity' => 1,
                        'price' => $product2->current_price   
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



        }

}
