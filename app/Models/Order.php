<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function lineItems() {
        return $this->hasMany(LineItem::class);
    }

    public function getOrderTotalAttribute() {
        return $this->total_price;    
    }

    public function lineItemsQuantity() {
        return $this->lineItems->sum('quantity');
    }

    public function calculateSubtotal() {
        $subtotal = $this->lineItems->sum(function($lineItem){
            return $lineItem->quantity * $lineItem->price;
        });

            if($subtotal == 0 || $subtotal > config('app.free_shipping_min_subtotal')) {
                $shipping_fee = 0;
            } else {
                $shipping_fee = config('app.shipping_fee');
            }

        $this->update([
            'shipping_fee' => $shipping_fee,
            'subtotal' => $subtotal,
            'total_price' => $subtotal + $shipping_fee
        ]);

        return number_format($subtotal, 2);
    }
}
