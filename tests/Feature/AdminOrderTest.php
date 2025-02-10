<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminOrderTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_order_edit_changes_are_saved_successfully() {
        $user = User::create([
            'name' => 'Michael Scott',    
            'email' => 'michaelScott@example.com',
            'password' => '12341312',
            'is_admin' => true
        ]);

        $this->actingAs($user);

        $shipping_methods = [
            'elta' => [
                'title' => 'Elta',
                'extra_cost' => 3.40,
            ],
            'fedex' => [
                'title' => 'Fedex',
                'extra_cost' => 5.00,
            ],
        ];

        $payment_methods = [
            'credit_card' => [
                'title' => 'Credit Card',
                'extra_cost' => 5.00,
            ],
            'bank_transfer' => [
                'title' => 'Bank Transfer',
                'extra_cost' => 2.00,
            ],
        ];

        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => 205,
            'subtotal' => 200,
            'status' => 'pending',
            'payment_method' => 'credit_card',
            'payment_fee' =>  $payment_methods['credit_card']['extra_cost'],
            'shipping_method' => 'elta',
            'shipping_fee' => $shipping_methods['elta']['extra_cost'],
            'paid' => false,
        ]);

        $response = $this->get(route('admin.order.edit.show', $order->id));

        $response->assertStatus(200);
        
        $response->assertSee($order->id);
        
        $this->patch(route('admin.order.edit.store',  ['id' => $order->id]),[
            'status' => 'completed',
            'payment_method' => 'bank_transfer',
            'shipping_method' => 'fedex',
            'paid' => true,
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('orders', ([
            'user_id' => $user->id,
            'total_price' => 202,
            'subtotal' => 200,
            'status' => 'completed',
            'payment_method' => 'bank_transfer',
            'payment_fee' =>  $payment_methods['bank_transfer']['extra_cost'],
            'shipping_method' => 'fedex',
            'shipping_fee' => 0,
            'paid' => true,
        ]));
    }
}
