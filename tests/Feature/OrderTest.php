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

        $user->currentOrder()->first()->calculateFeesAndPrices();

        $order->refresh();

        $this->assertEquals(108.4, $order->total_price);
        $this->assertEquals(100, $order->subtotal);
        $this->assertEquals(3.40, $order->shipping_fee);
        $this->assertEquals(5, $order->payment_fee);
        }
}
