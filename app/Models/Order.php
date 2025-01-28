<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'subtotal',    
        'total_price',
        'shipping_method',
        'shipping_fee',
        'payment_method',
        'payment_fee',
        'paid'    
    ];

    protected static function booted(){      
       
    }

    public function lineItems() {
        return $this->hasMany(LineItem::class);
    }

    public function getOrderTotalAttribute() {
        return $this->total_price;    
    }

    public function lineItemsQuantity() {
        return $this->lineItems->sum('quantity');
    }

    public function calculateOrderSummary() {
        $subtotal = $this->lineItems->sum(function($lineItem){
            return $lineItem->quantity * $lineItem->price;
        });

        $shippingMethods = config('app.shipping_methods');
        $defaultShippingMethod = array_key_first($shippingMethods);

            if($subtotal == 0 || $subtotal >= config('app.free_shipping_min_subtotal')) {
                $shipping_fee = 0;    
                $selectedShippingMethod = $defaultShippingMethod;
            } else {
                $selectedShippingMethod = $this->shipping_method ?? $defaultShippingMethod;
                $shipping_fee = $shippingMethods[$selectedShippingMethod]['extra_cost'];
               // if($selectedShippingMethod){
                   //$shipping_fee = $shippingMethods[$selectedShippingMethod]['extra_cost'];
               // } else {
                 //   $selectedShippingMethod = $defaultShippingMethod;
                  //  $shipping_fee = $shippingMethods[$defaultShippingMethod]['extra_cost'];
               // }
            }

        $this->update([
            'shipping_method' => $selectedShippingMethod,
            'shipping_fee' => $shipping_fee,
            'subtotal' => $subtotal,
            'total_price' => $subtotal + $shipping_fee + $this->payment_fee
        ]);
    }

    public function calculateFeesAndPrices() {

        $currentOrder = Auth::user()->currentOrder()->first();

        $currentOrder->update([
            'total_price' => $currentOrder->subtotal + $currentOrder->payment_fee + $currentOrder->shipping_fee,
        ]);  
    }

}
