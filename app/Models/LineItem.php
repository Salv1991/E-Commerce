<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',  
        'quantity',
        'price'
    ];

    protected static function booted(){
        static::created(function ($lineItem) {
            if ($lineItem->order->status === 'pending') {
                $lineItem->order->calculateOrderSummary();
            }
        });

        static::updated(function ($lineItem) {
            if ($lineItem->order->status === 'pending') {
                $lineItem->order->calculateOrderSummary();
            }
        });

        static::deleted(function ($lineItem) {
            if ($lineItem->order->status === 'pending') {
                $lineItem->order->calculateOrderSummary();
            }
        });
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function order(){
        return $this->belongsTo(Order::class);
    }
}

